<?php
class BillingReport
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
	public $params;
	public $rawParams;
	public $rawParamsExcel;

	function __construct()
	{
		 $this->table = 'billing';
		 $this->prefix = 'B';
	}

	function makeWildCard($text)
	{
		$text = trim($text);
		$text = str_replace(array("*","?"),array("%","_"),$text);
		return $text;
	}

	function getList($data, $user)
	{

		$param = array();
		$param['monthSearch']	= $_GET['monthSearch'];
		$param['yearSearch']	= $_GET['yearSearch'];
		$param['regionSearch']	= $_GET['regionSearch'];
		$param['statusSearch']	= $_GET['statusSearch'];

		$temp = array();
		foreach($param as $key => $value)
		{
			if($key == 'regionSearch' && $value != '')
			{
				foreach($value as $child)
					$temp[] = "regionSearch[]=".urlencode($child);
			}
			else
			{
				$temp[] = "{$key}=".urlencode($value);
			}
		}

		$this->params = implode('&', $temp);
		$this->rawParams = $param['monthSearch'].'/'.$param['yearSearch'];
		$this->rawParamsExcel = $param['monthSearch'].'/'.$param['yearSearch'];
		if($param['regionSearch'] != '')
		{
			$this->rawParams .= ' <br>Region : '.implode('<br>', $param['regionSearch']);
			$this->rawParamsExcel .= " \nRegion : ".implode("\n", $param['regionSearch']);
		}
		else
		{
			$this->rawParams .= ' <br>All Region';
			$this->rawParamsExcel .= ' \nAll Region';
		}
		$this->rawParams .= ' <br>Status : '.($param['statusSearch']=='-1' ? 'All' : $param['statusSearch']);
		$this->rawParamsExcel .= " \nStatus : ".($param['statusSearch']=='-1' ? 'All' : $param['statusSearch']);


		$where = array();

		if($data['monthSearch'] != '')
		{
			$where[] = "{$this->prefix}.month = '".mysql_real_escape_string($data['monthSearch'])."'";
		}

		if($data['yearSearch'] != '')
		{
			$where[] = "{$this->prefix}.year = '".mysql_real_escape_string($data['yearSearch'])."'";
		}

		if($data['statusSearch'] != '' && $data['statusSearch'] != '-1')
		{
			if($data['statusSearch'] == 'paid')
			{
				$where[] = "{$this->prefix}.amount > {$this->prefix}.remain_amount";
			}
			elseif($data['statusSearch'] == 'unpaid')
			{
				$where[] = "{$this->prefix}.amount = {$this->prefix}.remain_amount";
			}
		}

		if($data['regionSearch'] != '')
		{
			$childWhere = array();
			foreach($data['regionSearch'] as $region)
			{
				$childWhere[] = "C.region = '".mysql_real_escape_string($this->makeWildCard($region))."'";
			}
			$where[] = "(".implode(' OR ', $childWhere).")";
		}

		//now check for user right later
		if($user->type != ADMIN && $user->type != STAFF)
		{
			$where[] = "C.assigned_user = '".mysql_real_escape_string($user->id)."'";
		}

		if(!empty($where))
		{
			$where = 'WHERE '.implode(' AND ', $where);
		}
		else
		{
			$where = '';
		}

		$orderBy = "ORDER BY C.region, C.name";

		$current_page = $page;

    	$query = "SELECT COUNT(1) AS total
					FROM {$this->table} AS {$this->prefix}
					INNER JOIN contract AS C
						ON C.id = {$this->prefix}.contract_id
						AND C.status = 0
					$where ";
    	$results = mysql_query($query);
		$row = mysql_fetch_assoc($results);
    	$total_row = $row['total'];
		$this->totalRecord = $total_row;

    	if ($total_row != 0)
    	{
            $query = "SELECT {$this->prefix}.*,
						IF({$this->prefix}.amount > {$this->prefix}.remain_amount,'yes','no') AS paid,
						C.name,
						C.region,
						C.number,
						temp.check,
						temp1.billing_id AS haveNotes
						FROM {$this->table} AS {$this->prefix}
							INNER JOIN contract AS C
								ON C.id = {$this->prefix}.contract_id
								AND C.status = 0
							LEFT JOIN
								(
									SELECT GROUP_CONCAT(`check` SEPARATOR '\n') AS `check` , billing_id
									FROM payment
									GROUP BY billing_id
								) AS temp
								ON temp.billing_id = {$this->prefix}.id

							LEFT JOIN
								(
									SELECT billing_id
									FROM billing_notes
									GROUP BY billing_id
								) AS temp1
								ON temp1.billing_id = {$this->prefix}.id
						$where
						$orderBy";

            $results = mysql_query($query);
			return $results;
    	}
		return false;
	}
}