<?php
class save
{
	private $database;
	private $crawler;
	private $novelTable = 'jm_bage_novel';
	private $chapterTable = 'jm_bage_chapter';
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
			$this->database->insertData($this->novelTable, $novelData);
			$novelId = $this->database->getLastId();
			$chapterData = $this->crawler->crawlChapter($originalId, $novelId);
			$this->database->multipleInsert($this->chapterTable, $chapterData);
			//更新最新章节id和最新章节名;
			$lastData = $this->getLastInfo($chapterData);
			$this->database->updateDataById($this->novelTable, $lastData, $novelId);
			echo "已爬取完一篇小说{$originalId}\n\n";
		}
	}

	//获取最新章节id和最新章节名
	public function getLastInfo($chapterData)
	{
		$lastChapterId = $this->database->getMaxId($this->chapterTable);
		$count = count($chapterData);
		$temp = array_pop($chapterData);
		$result = [
			'lastChapterName' => $temp['title'],
			'lastChapterId' => $lastChapterId,
			'chapterNum' => $count,
		];
		return $result;
	}
}