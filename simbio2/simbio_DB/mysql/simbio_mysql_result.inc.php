<?php

// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} elseif (INDEX_AUTH != 1) { 
    die("can not access this file directly");
}

class simbio_mysql_result extends simbio
{
    /**
     * Private properties
     */
    private $res_result = false;
    private $sql_string = '';
    private $res_conn = '';

    /**
     * Public properties
     */
    public $num_rows = 0;
    public $field_count = 0;
    public $affected_rows = 0;
    public $insert_id = 0;
    public $errno = false;


    /**
     * Class Constructor
     *
     * @param   string      $str_query
     * @param   resource    $res_conn
     */
    public function __construct($str_query, $res_conn)
    {
        $this->sql_string = trim($str_query);
        $this->sendQuery($res_conn,$str_query);
    }


    /**
     * Method to send SQL query
     *
     * @param   resource    $res_conn
     * @return  void
     */
    private function sendQuery($res_conn,$sql_string)
    {
        $this->sql_string=$sql_string;
        global $res_conn;
        // checking query type
        // if the query return recordset or not
        if (preg_match("/^SELECT|DESCRIBE|SHOW|EXPLAIN\s/i", $this->sql_string)) {
            $this->res_result = @mysqli_query($this->sql_string, $this->$res_conn);
            // error checking
            if (!$this->res_result) {
                $this->error = 'Query ('.$this->sql_string.") failed to executed. Please check your query again \n".mysqli_error($this->$res_conn);
                $this->errno = mysqli_errno($this->$res_conn);
            } else {
                // count number of rows
                $this->num_rows = @mysqli_num_rows($this->res_result);
                $this->field_count = @mysqli_num_fields($this->res_result);
            }
        } else {
            $query = @mysqli_query($this->sql_string, $this->$res_conn);
            $this->insert_id = @mysqli_insert_id($this->$res_conn);
            // error checking
            if (!$query) {
                $this->error = 'Query ('.$this->sql_string.") failed to executed. Please check your query again \n".mysqli_error($this->$res_conn);
                $this->errno = mysqli_errno($this->$res_conn);
            } else {
                // get number of affected row
                $this->affected_rows = @mysqli_affected_rows($this->$res_conn);
            }
            // nullify query
            $query = null;
        }
    }


    /**
     * Method to fetch record in associative  array
     *
     * @return  array
     */
    public function fetch_assoc($res_result)
    {
        $this->res_result = $res_result;
        return @mysqli_fetch_assoc($this->res_result);
    }


    /**
     * Method to fetch record in numeric array indexes
     *
     * @return  array
     */
    public function fetch_row($res_result)
    {
        $this->res_result=$res_result;
        return @mysqli_fetch_row($this->res_result);
    }


    /**
     * Method to fetch fields information of resultset
     *
     * @return  array
     */
    public function fetch_fields($res_result)
    {
        $this->res_result=$res_result;
        $_fields_info = array();
        $_f = 0;
        $_field_num = mysqli_num_fields($this->res_result);
        while ($_f < $_field_num) {
            $_fields_info[] = mysqli_fetch_field($this->res_result, $_f);
            $_f++;
        }

        return $_fields_info;
    }


    /**
     * Method to free resultset memory
     *
     * @return  void
     */
    public function free_result()
    {
        if ($this->res_result) {
            @mysqli_free_result($this->res_result);
        }
    }
}
?>
