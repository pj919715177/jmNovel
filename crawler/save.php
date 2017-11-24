<?php
class save
{
	private $database;
	private $crawler;
	private $novelTable = 'jm_bage_novel';
	private $chapterTable = 'jm_bage_chapter';
	private $errorsTable = 'jm_bage_errors_log';
	public function __construct($database, $crawler)
	{
		$this->database = $database;
		$this->crawler = $crawler;
	}

	//爬取章节并存入数据库
	public function saveData($originalId)
	{
		$novelData = $this->crawler->getNovel($originalId);
		if ($novelData) {
			if (!isset($novelData['novelName'])) {
				$this->handleErrors('无法匹配小说信息', $originalId);
				return;
			}
			try {
				//开始事务
				$this->database->start();
				$this->database->insertData($this->novelTable, $novelData);
				$novelId = $this->database->getLastId();
				$chapterData = $this->crawler->crawlChapter($originalId, $novelId);
				$lastId = $this->database->multipleInsert($this->chapterTable, $chapterData);
				//更新最新章节id和最新章节名;
				$lastData = $this->getLastInfo($chapterData, $novelId);
				$this->database->updateDataById($this->novelTable, $lastData, $novelId);
				echo "已爬取完一篇小说{$originalId}\n\n";
				//提交事务
				$this->database->end();
			} catch (Exception $e) {
				//事务回滚
				$this->database->back();
				$this->handleErrors($e->getMessage(), $originalId);
			}
		}
	}

	//获取最新章节id和最新章节名
	public function getLastInfo($chapterData, $novelId)
	{
		$lastId = $this->database->getMaxId($novelId);
		$count = count($chapterData);
		$temp = array_pop($chapterData);
		$result = [
			'lastChapterName' => $temp['title'],
			'lastChapterId' => $lastId,
			'chapterNum' => $count,
		];
		return $result;
	}

	//事务出错处理//出错处理
	public function handleErrors($message, $originalId)
	{
		//记录错误
		$data = [
			'originalId' => $originalId,
			'message' => $message,
			'createTime' => time(),
		];
		$this->database->insertData($this->errorsTable, $data);
	}

}