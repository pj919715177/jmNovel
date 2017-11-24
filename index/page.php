<?php
class page
{
	private $pageNum;
	private $pageSize;
	private $dataCount;

	public function __construct($pageNum = 1, $pageSize = 20, $dataCount = 0)
	{
		$this->pageNum = $pageNum;
		$this->pageSize = $pageSize;
		$this->dataCount = $dataCount;
	}

	public function setPageNum($pageNum)
	{
		$this->pageNum = $pageNum;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
	}

	public function setDataCount($dataCount)
	{
		$this->dataCount = $dataCount;
	}

	public function showPage()
	{
		$start = $this->pageNum - 2;
		$start < 1 && $start = 1;
		$last = $this->pageNum - 1;
		$next = $this->pageNum + 1;
		//样式设置
		$lastCount = ($last - 1) * $this->pageSize;
		$lastClass = ($lastCount >= 0 && $lastCount <= $this->dataCount) ? '' : 'disabled';
		$nextCount = ($next - 1) * $this->pageSize;
		$nextClass = ($nextCount >= 0 && $nextCount <= $this->dataCount) ? '' : 'disabled';

		//打印页码
		echo '<nav aria-label="Page navigation" class="page">';
		echo '<ul class="pagination">';
		echo "<li class='{$lastClass}'>";
		echo "<a href='index.php?pageNum={$last}' aria-label='Previous' >";
		echo '<span aria-hidden="true">&laquo;</span>';
		echo '</a>';
		echo '</li>';

		for ($i=0; $i < 5; $i++, $start++) {
			$tempClass = '';
			$tempCount = ($start - 1) * $this->pageSize;
			($tempCount < 0 || $tempCount > $this->dataCount) && $tempClass .= 'disabled ';
			$start == $this->pageNum && $tempClass .= 'active ';
			$tempClass = rtrim($tempClass);

			echo "<li class='{$tempClass}'>";
			echo "<a href='index.php?pageNum={$start}'>{$start}</a>";
			echo '<li>';
		}

		echo "<li class='{$nextClass}'>";
		echo "<a href='index.php?pageNum={$next}' aria-label='Previous' >";
		echo '<span aria-hidden="true">&raquo;</span>';
		echo '</a>';
		echo '</li>';
		echo '</ul>';
		echo '</nav>';
	}
}