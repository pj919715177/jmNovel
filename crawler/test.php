<?php
set_time_limit(0);
require_once('crawler.php');
require_once('datebase.php');
require_once('save.php');

header("Content-type:text/html;charset=utf-8");
$crawler = new crawler();
$datebase = new datebase();

$save = new save($datebase, $crawler);
//50000
for ($index = 1; $index < 50000; $index++) {
	$save->saveData($index);
}
echo "爬取完毕！\n";
