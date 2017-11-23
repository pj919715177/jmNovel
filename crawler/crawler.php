<?php
class crawler
{
	private $curl;
	private $originalId;
	private $novelHtml;
	//被爬网站域名
	private $crawlerUrl = 'http://www.qu.la';

	//小说基本信息正则
	private $novelBasePattern = '/<div id="maininfo">\s*?<div id="info">\s*?<h1>(\S*?)<\/h1>\s*?<p>作&nbsp;&nbsp;者：(\S*?)<\/p>[\s\S]*?<\/div>\s*?<div id="intro">([\s\S]*?)<\/div>/';
	
	private $allLinkPattern = '/<div id="list">\s*?<dl>([\s\S]*?)<\/dl>\s*?<\/div>/';
	//章节正文
	private $chapterContentPattern = '/<div id="content">([\s\S]*?)<\/div>/';

	public function __construct()
	{
	    $this->curl = curl_init();
	    curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0');
	    // 在HTTP请求头中"Referer: "的内容。
	    curl_setopt($this->curl, CURLOPT_REFERER,"https://www.baidu.com/s?word=%E7%9F%A5%E4%B9%8E&tn=sitehao123&ie=utf-8&ssl_sample=normal&f=3&rsp=0");
	    curl_setopt($this->curl, CURLOPT_ENCODING, "gzip, deflate, sdch");
	    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($this->curl, CURLOPT_TIMEOUT, 0);
	    $this->setFakeIp();
	}

	//获取ip
	private function getIp()
	{
		$numArr = ["218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211"];
		$count = count($numArr);
		$result = '';
		for ($i = 0; $i < 4; $i++) {
    		$index = mt_rand(0, $count-1);
    		$result .= $numArr[$index] . '.';
		}
    	return rtrim($result, '.');
	}

	//伪装ip
	private function setFakeIp()
	{
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['X-FORWARDED-FOR:' . $this->getIp(), 'CLIENT-IP:' . $this->getIp()]);
	}

	//爬小说列表页面
	public function getNovel($originalId)
	{
		$this->setFakeIp();
	    $originalId = (int)$originalId;
	    $url = $this->crawlerUrl . '/book/' . $originalId;
	    curl_setopt($this->curl, CURLOPT_URL, $url);
	    $html = curl_exec($this->curl);
	    if (!$html) {
	    	return [];
	    }
	    $this->originalId = $originalId;
	    $this->novelHtml = $html;
	    return $this->handleNovel($html);
	}

	//提取小说信息（小说基本信息）
	public function handleNovel($html)
	{
	    //基本信息
	    $pattern = $this->novelBasePattern;
	    $result = preg_match($pattern, $html, $match);
	    $data = [
		    'originalId' => $this->originalId, 
		];
		if ($result) {
			$data['novelName'] = $match[1];
			$data['novelAuthor'] = $match[2];
			$data['introduce'] = trim($match[3]);
		}
		return $data;
	}

	//提取章节url，循环爬取章节（章节名、url细节，小说id，页面编码）
	public function crawlChapter($originalId, $novelId)
	{
		if ($originalId != $this->originalId) {
			return [];
		}

		$html = $this->novelHtml;
	    $data = [];
	    $pattern = $this->allLinkPattern;
	    $result = preg_match($pattern, $html, $match);
	    if ($result && isset($match[1]) && $match[1]) {
	        $pattern = "/<dd>\s*?<a style=\"([\s\S]*?)\" href=\"\/book\/{$originalId}\/(\d*?).html\">([\s\S]*?)<\/a>\s*?<\/dd>/";
	        preg_match_all($pattern, $match[1], $out, PREG_SET_ORDER);
	        foreach ($out as $key => $item) {
	            $url = $this->crawlerUrl . "/book/{$originalId}/{$item[2]}.html";
	            $content = $this->getChapter($url);
	            $temp = ['novelId' => $novelId, 'title' => $item[3], 'content' => $content, 'pageNum' => $item[2]];
	            $data[] = $temp;
	            echo "已爬取一个章节{$item[2]}\n";
	        }
	    }
	    return $data;
	}

	//通过章节url爬取章节信息（章节内容）
	public function getChapter($url)
	{
		$this->setFakeIp();
	    curl_setopt($this->curl, CURLOPT_URL, $url);
	    $html = curl_exec($this->curl);
	    return $this->handleChapter($html);
	}

	//提取章节信息
	public function handleChapter($html)
	{
	    //匹配章节内容
	    $pattern = $this->chapterContentPattern;
	    preg_match($pattern, $html, $match);
	    if (!isset($match[1])) {
	    	return '';
	    }
	    //去除广告
	    $str = '<script>chaptererror();</script>';
	    $content = str_replace($str, '', $match[1], $count);
	    if ($count) {
	        $content = substr($content, 0, strrpos($content, "<br>"));
	    }
	    return $content;
	} 

	public function __destruct()
	{
		curl_close($this->curl);
	}
}