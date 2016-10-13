<?php
class ContractUser
{

	public $paging ;
	public $totalRecord;
	public $range;
    public $totalPage;
	public $currentPage;
	protected $table;
	public $sortDirection;
	public $sortColumn;

	function __construct()
	{
		 $this->table = 'contract_user';
	}
	
	function makeWildCard($text)
	{
		$text = trim($text);
		$text = str_replace(array("*","?"),array("%","_"),$text);
		return $text;
	}

	function getList($data, $user)
	{
		$page = $data['current_page'];
		$this->sortDirection = $sortDirection = $data['sortDirection'];
		$this->sortColumn = $sortColumn	= $data['sortColumn'];

		$where = array();
		if($data['usernameSearch'] != '')
		{
			$where[] = "username LIKE '".mysql_real_escape_string($this->makeWildCard($data['usernameSearch']))."'";
		}

		if($user->type != ADMIN)
		{
			$where[] = "username = '".mysql_real_escape_string($user->username)."'";
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
					FROM {$this->table}
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

            $query = "SELECT *
						FROM {$this->table}
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