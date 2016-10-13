<?php

class PagingHelper
{
	public static function getPagingFront($total_page, $current)
	{
		if ($total_page == 0)
		{
			return '';
		}

		if ($current > $total_page)
		{
			$current = $total_page;
		}

		$temp = array();

        $from = (ceil($current/WEBSITE_DEFAULT_NUMBER_LINK) * WEBSITE_DEFAULT_NUMBER_LINK) - WEBSITE_DEFAULT_NUMBER_LINK + 1;

        if($from > WEBSITE_DEFAULT_NUMBER_LINK)
            $temp[] = '<span class="pageNum"><a href="#" rel="'. ($from - 1) .'" class="paging"><<</a></span>';

        $to = $from + WEBSITE_DEFAULT_NUMBER_LINK - 1;
        if($to >= $total_page)
        {
            $to     = $total_page;
            $haveNext = false;
        }
        else
        {
            $haveNext = true;
        }

        for ($i = $from ; $i <= $to ; $i++)
        {
            if ($i == $current)
            {
                $temp[] = '<span class="current">'.$i.'</span>';
            }
            else
            {
                $temp[] = '<span class="pageNum"><a href="#" rel="'.$i.'" class="paging">'.$i.'</a></span>';
            }
        }

        if($haveNext)
        {
            $temp[] = '<span class="pageNum"><a href="#" rel="'. ($to + 1) .'" class="paging">>></a></span>';
        }

		return implode("",$temp);
	}
}