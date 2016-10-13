<?php
class Billing
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
		 $this->table = 'billing';
		 $this->prefix = 'B';
		 
		 $this->contractTable = 'contract';
		 $this->contractPrefix = 'C';
		 
	}

	function getList($data, $user)
	{
		$page = $data['current_page'];
		$this->sortDirection = $sortDirection = $data['sortDirection'];
		$this->sortColumn = $sortColumn	= $data['sortColumn'];

		$where = array();
		if($data['numberSearch'] != '')
		{
			$where[] = "{$this->contractPrefix}.number = '".mysql_real_escape_string($data['numberSearch'])."'";
		}

		if($data['nameSearch'] != '')
		{
			$where[] = "{$this->contractPrefix}.name = '".mysql_real_escape_string($data['nameSearch'])."'";
		}
		
		if($data['startDateSearch'] != '')
		{
			$where[] = "{$this->contractPrefix}.start_date = '".date("Y-m-d", strtotime($data['startDateSearch']))."'";
		}

		if($data['assignedUserSearch'] != '')
		{
			$where[] = "{$this->contractPrefix}.assigned_user = '".mysql_real_escape_string($data['assignedUserSearch'])."'";
		}

		//now check for user right
		if($user->type != ADMIN)
		{
			$where[] = "{$this->contractPrefix}.assigned_user = '".mysql_real_escape_string($user->id)."'";
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

    	$query = "SELECT count(1) AS total
					FROM
					(
						SELECT DISTINCT(contract_id), year
						FROM {$this->table}
					) AS {$this->prefix}
					INNER JOIN {$this->contractTable} AS {$this->contractPrefix}
					ON {$this->prefix}.contract_id = {$this->contractPrefix}.id
					$where
					";
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
							{$this->contractPrefix}.*,
							{$this->contractPrefix}.start_date AS start_date_formatted,
							contract_user.username
						FROM
						(
							SELECT DISTINCT(contract_id), year
							FROM {$this->table}
						) AS {$this->prefix}
							INNER JOIN {$this->contractTable} AS {$this->contractPrefix}
								ON {$this->prefix}.contract_id = {$this->contractPrefix}.id
							INNER JOIN contract_user
									ON contract_user.id = {$this->contractPrefix}.assigned_user	
						$where
						$orderBy
						LIMIT $offset,$limit";
echo $query;die;
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