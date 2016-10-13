<?php
class User
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
		 $this->table = 'users';
		 $this->prefix = 'U';
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

		if($data['usernameSearch'] != '')
		{
			$where[] = "{$this->prefix}.username LIKE '".mysql_real_escape_string($this->makeWildCard($data['usernameSearch']))."'";
		}

		if($data['typeSearch'] != '')
		{
			$where[] = "{$this->prefix}.type = '".mysql_real_escape_string($this->makeWildCard($data['typeSearch']))."'";
		}

		if($data['statusSearch'] != '')
		{
			$where[] = "{$this->prefix}.status = '".mysql_real_escape_string($this->makeWildCard($data['statusSearch']))."'";
		}

		//if not admin, only can see it self
		if(!(isAdmin()))
		{
			$userInfo = getUserInfo();
			$where[] = "{$this->prefix}.id = '".mysql_real_escape_string($userInfo['id'])."'";
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
							DATE_FORMAT(date_created, '%m/%d/%y %H:%i') AS dateCreated,
							DATE_FORMAT(date_modified, '%m/%d/%y %H:%i') AS dateModified

						FROM {$this->table} AS {$this->prefix}

						$where
						$orderBy
						LIMIT $offset,$limit";

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