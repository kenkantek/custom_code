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
		 $this->table = 'contract';
		 $this->prefix = 'C';
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
		
		if($data['invoiceIdSearch'] != '')
		{
			//$where[] = "{$this->prefix}.number = '".mysql_real_escape_string($data['invoiceIdSearch'])."'";
			//firt find contract have this invoice
			$query = "SELECT contract_id FROM billing WHERE invoice_id = '".mysql_real_escape_string($data['invoiceIdSearch'])."'";
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 0)
			{
				$where[] = "{$this->prefix}.id = ''";
			}
			else
			{
				$row = mysql_fetch_assoc($result);
				$where[] = "{$this->prefix}.id = '{$row['contract_id']}'";
			}
		}
		
		if($data['numberSearch'] != '')
		{
			$where[] = "{$this->prefix}.number LIKE '".mysql_real_escape_string($this->makeWildCard($data['numberSearch']))."'";
		}

		if($data['nameSearch'] != '')
		{
			$where[] = "{$this->prefix}.name LIKE '".mysql_real_escape_string($this->makeWildCard($data['nameSearch']))."'";
		}
		
		if($data['emailSearch'] != '')
		{
			$where[] = "{$this->prefix}.email LIKE '".mysql_real_escape_string($this->makeWildCard($data['emailSearch']))."'";
		}
		
		if($data['startDateSearch'] != '')
		{
			$where[] = "{$this->prefix}.start_date = '".date("Y-m-d", strtotime($data['startDateSearch']))."'";
		}

		if($data['assignedUserSearch'] != '')
		{
			$where[] = "{$this->prefix}.assigned_user = '".mysql_real_escape_string($data['assignedUserSearch'])."'";
		}

		//now check for user right
		if($user->type != ADMIN && $user->type != STAFF && $user->type != SUPER)
		{
			$where[] = "{$this->prefix}.assigned_user = '".mysql_real_escape_string($user->id)."'";
		}

		if($data['statusSearch'] != 'all')
		{

			$currentMonth = date('m');
			$previousMonth = $currentMonth - 1;
			if($previousMonth == 0)
			{
				$currentYear = date('Y');
				if($currentYear == $data['currentYear'])
					$previousMonth = 1;
				else
					$previousMonth = 12;
			}
			//firt find contract on this year
			$query = "SELECT contract_id FROM billing
						WHERE year = '{$data['currentYear']}'
							AND month = '$previousMonth'";

			$result = mysql_query($query);
			if(mysql_num_rows($result) == 0)
			{
				$contract_id = "''";
			}
			else
			{
				$contract_id = array();
				while($row = mysql_fetch_assoc($result))
				{
					$contract_id[] = $row['contract_id'];
				}
				$contract_id = implode(',', $contract_id);
			}

			if($data['statusSearch'] == 'unverified')
			{
				$where[] = "{$this->prefix}.id NOT IN ($contract_id)";
			}
			elseif($data['statusSearch'] == 'verified')
			{
				$where[] = "{$this->prefix}.id  IN ($contract_id)";
			}
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
							DATE_FORMAT({$this->prefix}.start_date,'%m/%d/%Y') AS start_date_formatted,
							contract_user.username
						FROM {$this->table} AS {$this->prefix}
							INNER JOIN contract_user
								ON contract_user.id = {$this->prefix}.assigned_user
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