<?php
// be sure that this file not accessed directly
if (INDEX_AUTH != 1) {
    die("can not access this file directly");
}

class report_datagrid extends simbio_datagrid
{
    public $paging_set = null;
    public $using_AJAX = false;
    public $show_spreadsheet_export = false;
    public $spreadsheet_export_btn = '';

    public function __construct()
    {
        // set default table and table header attributes
        $this->table_attr = 'align="center" class="dataListPrinted" cellpadding="3" cellspacing="1"';
        $this->table_header_attr = 'class="dataListHeaderPrinted"';

        $this->spreadsheet_export_btn = '&nbsp;<a href="../spreadsheet.php" class="s-btn btn btn-success">'.('Export to spreadsheet format').'</a>';
    }

    /**
     * Modified method to make HTML output more friendly to printer
     *
     * @param   object  $obj_db
     * @param   integer $int_num2show
     * @return  string
     */
    protected function makeOutput($int_num2show = 30)
    {
        // remove invisible field
        parent::removeInvisibleField();
        // disable row highlight
        $this->highlight_row = false;
        // get fields array and set the table header
        $this->setHeader($this->grid_result_fields);

        $_record_row = 1;
        // data loop
        foreach ($this->grid_result_rows as $_data) {
            // alternate the row color
            $_row_class = ($_record_row%2 == 0)?'alterCellPrinted':'alterCellPrinted2';

            // append array to table
            $this->appendTableRow($_data);

            // field style modification
            foreach ($this->grid_result_fields as $_idx => $_fld) {
                // checking for special field width value set by column_width property array
                $_row_attr = 'valign="top"';
                $_classes = $_row_class;
                if (isset($this->column_width[$_idx])) {
                    $_row_attr .= ' style="width: '.$this->column_width[$_idx].';"';
                }
                $this->setCellAttr($_record_row, $_idx, $_row_attr.' class="'.$_classes.'"');
            }
            $_record_row++;
        }

        // init buffer return var
        $_buffer = '';

        // create paging
        if ($this->num_rows > $int_num2show) {
            $this->paging_set = simbio_paging::paging($this->num_rows, $int_num2show, 10, '', 'reportView');
        } else {
            $this->paging_set =  '&nbsp;';
        }
        $_buffer .= '<div class="s-print__page-info printPageInfo"><strong>'.$this->num_rows.'</strong> '.('record(s) found. Currently displaying page').' '.$this->current_page.' ('.$int_num2show.' '.('record each page').')';
        // put the additional button process
        if($this->show_spreadsheet_export) {
            $_buffer .= $this->spreadsheet_export_btn;
        }
        $_buffer .= '</div>'."\n"; //mfc
        $_buffer .= $this->printTable();

        return $_buffer;
    }
}
