<?php
class Cars
{

	public $paging ;
	public $totalRecord;
	public $range;
    public $totalPage;
	public $currentPage;
	protected $table;
	public $sortDirection;
	public $sortColumn;
	protected $prefix;

	function __construct()
	{
		 $this->table = 'cars';
		 $this->prefix = 'C';
	}

	function makeWildCard($text)
	{
		$text = trim($text);
		$text = str_replace(array("*","?"),array("%","_"),$text);
		return $text;
	}

	function getList($data)
	{
		$page = $data['page'];
		$this->sortDirection = $sortDirection = $data['sortDirection'];
		$this->sortColumn = $sortColumn	= $data['sortColumn'];

		$where = array();

		if($data['nameSearch'] != '')
		{
			$where[] = "{$this->prefix}.name LIKE '".mysql_real_escape_string($this->makeWildCard($data['nameSearch']))."'";
		}

		if($data['categorySearch'] != '')
		{
			$where[] = "{$this->prefix}.id_category = '".mysql_real_escape_string($this->makeWildCard($data['categorySearch']))."'";
		}

		if($data['statusSearch'] != '')
		{
			$where[] = "{$this->prefix}.status = '".mysql_real_escape_string($this->makeWildCard($data['statusSearch']))."'";
		}

		if(!empty($where))
		{
			$where = 'WHERE '.implode(' AND ', $where);
		}
		else
		{
			$where = '';
		}

		$orderBy = "ORDER BY $sortColumn $sortDirection";

		$current_page = $page;

    	$query = "SELECT COUNT(1) AS total
					FROM {$this->table} AS {$this->prefix}
					$where ";
    	$results = mysql_query($query);
		$row = mysql_fetch_assoc($results);
    	$total_row = $row['total'];
		$this->totalRecord = $total_row;

    	if ($total_row != 0)
    	{
    	    $total_page = ceil($total_row/ROW_PER_PAGE);
    		if ($current_page > $total_page)
    		{
    			$current_page = $total_page;
    		}
	        $offset = ( $current_page - 1 ) * ROW_PER_PAGE;
            $limit = ROW_PER_PAGE;

            $query = "SELECT {$this->prefix}.*,
							category.name AS category_name,
							DATE_FORMAT(date_created, '%m/%d/%y %H:%i') AS dateCreated,
							IF(countImage.totalImage IS NULL, 0, countImage.totalImage) AS totalImage

						FROM {$this->table} AS {$this->prefix}
							INNER JOIN category
								ON category.id = {$this->prefix}.id_category
							LEFT JOIN
								(SELECT COUNT(1) AS totalImage, id_car
								FROM car_gallery
								GROUP BY id_car) AS countImage
								ON countImage.id_car = {$this->prefix}.id
						$where
						$orderBy
						LIMIT $offset,$limit";

			//echo $query;die;

            $results = mysql_query($query);
            $this->paging = PagingHelper::getPagingFront($total_page, $current_page);
			$this->range  = ($offset+1).'-'.($offset + $limit+1);
            $this->totalPage = $total_page;
            $this->currentPage = $current_page;

			return $results;
    	}
		return false;
	}
}