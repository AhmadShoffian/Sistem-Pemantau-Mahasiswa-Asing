<?php
// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} elseif (INDEX_AUTH != 1) { 
    die("can not access this file directly");
}

class simbio_paging
{
    /**
     * Static Method to print out the paging list
     *
     * @param   integer $int_all_recs_num
     * @param   integer $int_recs_each_page
     * @param   integer $int_pages_each_set
     * @param   string  $str_fragment
     * @param   string  $str_target_frame
     * @return  string
     */
    public static function paging($int_all_recs_num, $int_recs_each_page, $int_pages_each_set = 10, $str_fragment = '', $str_target_frame = '_self')
    {
        // check for wrong arguments
        if ($int_recs_each_page > $int_all_recs_num) {
            return;
        }

        // total number of pages
        $_num_page_total = ceil($int_all_recs_num/$int_recs_each_page);

        if ($_num_page_total < 2) {
            return;
        }

        // total number of pager set
        $_pager_set_num = ceil($_num_page_total/$int_pages_each_set);

        // check the current page number
        if (isset($_GET['page']) AND $_GET['page'] > 1) {
            $_page = (integer)$_GET['page'];
        } else {$_page = 1;}

        // check the query string
        if (isset($_SERVER['QUERY_STRING']) AND !empty($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $arr_query_var);
            // rebuild query str without "page" var
            $_query_str_page = '';
            foreach ($arr_query_var as $varname => $varvalue) {
                if (is_string($varvalue)) {
                    $varvalue = urlencode($varvalue);
                    if ($varname != 'page') {
                        $_query_str_page .= $varname.'='.$varvalue.'&';
                    }
                } else if (is_array($varvalue)) {
                    foreach ($varvalue as $e_val) {
                        if ($varname != 'page') {
                            $_query_str_page .= $varname.'[]='.$e_val.'&';
                        }
                    }
                }
            }
            // append "page" var at the end
            $_query_str_page .= 'page=';
            // create full URL
            $_current_page = $_SERVER['PHP_SELF'].'?'.$_query_str_page;
        } else {
            $_current_page = $_SERVER['PHP_SELF'].'?page=';
        }

        // target frame
        $str_target_frame = 'target="'.$str_target_frame.'"';

        // init the return string
        $_buffer = '<span class="pagingList">';
        $_stopper = 1;

        // count the offset of paging
        if (($_page > 5) AND ($_page%5 == 1)) {
            $_lowest = $_page-5;
            if ($_page == $_lowest) {
                $_pager_offset = $_lowest;
            } else {
                $_pager_offset = $_page;
            }
        } else if (($_page > 5) AND (($_page*2)%5 == 0)) {
            $_lowest = $_page-5;
            $_pager_offset = $_lowest+1;
        } else if (($_page > 5) AND ($_page%5 > 1)) {
            $_rest = $_page%5;
            $_pager_offset = $_page-($_rest-1);
        } else {
            $_pager_offset = 1;
        }

        // Previous page link
				$_first = ('First Page');

				$_prev = ('Previous');

        if ($_page > 1) {
            $_buffer .= ' &nbsp;';
            $_buffer .= '<a href="'.$_current_page.(1).$str_fragment.'" '.$str_target_frame.' class="first_link">'.$_first.'</a>&nbsp; '."\n";
            $_buffer .= ' &nbsp;';
            $_buffer .= '<a href="'.$_current_page.($_page-1).$str_fragment.'" '.$str_target_frame.' class="prev_link">'.$_prev.'</a>&nbsp; '."\n";
        }

        for ($p = $_pager_offset; ($p <= $_num_page_total) AND ($_stopper < $int_pages_each_set+1); $p++) {
            if ($p == $_page) {
                $_buffer .= ' &nbsp;<b>'.$p.'</b>&nbsp; '."\n";
            } else {
                $_buffer .= ' &nbsp;';
                $_buffer .= '<a href="'.$_current_page.$p.$str_fragment.'" '.$str_target_frame.'>'.$p.'</a>&nbsp; '."\n";
            }

            $_stopper++;
        }

        // Next page link
				$_next = ('Next');

        if (($_pager_offset != $_num_page_total-4) AND ($_page != $_num_page_total)) {
            $_buffer .= ' &nbsp;';
            $_buffer .= '<a href="'.$_current_page.($_page+1).$str_fragment.'" '.$str_target_frame.' class="next_link">'.$_next.'</a>&nbsp; '."\n";
        }

        // Last page link
				$_last = ('Last Page');

        if ($_page < $_num_page_total) {
            $_buffer .= ' &nbsp;';
            $_buffer .= '<a href="'.$_current_page.($_num_page_total).$str_fragment.'" '.$str_target_frame.' class="last_link">'.$_last.'</a>&nbsp; '."\n";
        }

        $_buffer .= '</span>';

        return $_buffer;
    }
}
