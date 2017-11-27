<?php
header("Content-type:text/html;charset=utf-8");
require_once('datebase.php');
require_once('page.php');
//获取页数
$pageNum = 1;
isset($_GET['pageNum']) && $pageNum = (int)$_GET['pageNum'];
$pageNum < 1 && $pageNum = 1;
$datebase = new datebase();

$table = 'jm_bage_novel';
//获取数据总数
$count = 0;
$countData = $datebase->getDataDetail($table, 'count(id) as count');
$countData && $count = $countData['count'];

//设置分页
$page = new page($pageNum, 20, $count);
//获取数据详情
$select = 'id,novelName,novelAuthor,lastChapterName,lastChapterId';
$offset = 20 * ($pageNum - 1);
$result = $datebase->getDataList($table, $select, 'novelType IN(1,2,3,4,5,6) AND feature in(1,2,3)', [], 'id desc', 20, $offset);
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
.page{
	margin-left: 10%;
}
</style>


<div class="novelList head">
<h1 class="title"><a href="/index.php">简明小说</a></h1>
</div>

<div class="panel panel-default novelList">
  <!-- Default panel contents -->
  <div class="panel-heading">最新小说</div>

  <table class="table">
  	<?php foreach ($result as $item) {?>
	    <tr>
	    	<td><a href="/chapterList.php?novelId=<?php echo $item['id']?>"><?php echo $item['novelName'];?></a></td>
	    	<td><a href="/content.php?chapterId=<?php echo $item['lastChapterId']?>"><?php echo $item['lastChapterName'];?></a></td>
	    	<td><?php echo $item['novelAuthor'];?></td>
	    <tr>
	<?php } ?>
  </table>
</div>

<?php $page->showPage();?>