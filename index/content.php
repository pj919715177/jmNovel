<?php
header("Content-type:text/html;charset=utf-8");
require_once('datebase.php');
$datebase = new datebase();

$chapterId = $_GET['chapterId'];

//获取章节详细数据
$table = 'jm_bage_chapter';
$select = 'novelId,title,content';
$where = 'id=:id';
$param = [':id' => $chapterId];
$chapterData = $datebase->getDataDetail($table, $select, $where, $param);

$novelId = $chapterData['novelId'];
//获取小说数据
$table = 'jm_bage_novel';
$select = 'novelName,novelAuthor,introduce';
$where = 'id=:id';
$param = [':id' => $novelId];
$novelData = $datebase->getDataDetail($table, $select, $where, $param);
?>

<!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<style>
.novelList{
	width:80%;
	min-width: 600px;
	margin: auto;
}
.head{
	margin-bottom: 5px;
	border:2px solid black;
	border-radius:5px;
	height: 70px;
}
.title{
	margin-top:3px;
	margin-left: 3px;
	color: blue;
}
.novelData{
	border: 1px solid gray;
	border-radius:5px;
}
</style>
<div class="novelList head">
<h1 class="title"><a href="/index.php">简明小说</a></h1>
</div>

<div class="novelList novelData">
<h3><a href="chapterList.php?novelId=<?php echo $novelId;?>"><?php echo $novelData['novelName'];?></a></h3>
<div>作者：<?php echo $novelData['novelAuthor'];?></div>
<p><?php echo $novelData['introduce'];?></p>
</div>
<div class="novelList novelData">
<h4><?php echo $chapterData['title'];?></h4>
<p><?php echo $chapterData['content'];?></p>
</div>