<?php
/**
 * simbio_form_table
 * Class for creating form with HTML table layout
 *
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} elseif (INDEX_AUTH != 1) {
    die("can not access this file directly");
}

require 'simbio_form_maker.inc.php';

class simbio_form_table extends simbio_form_maker
{
    public $table_attr;
    public $table_header_attr;
    public $table_content_attr;
    public $submit_button_attr = 'name="submit" value="Save Data"';
    public $with_form_tag = true;
    public $edit_mode = false;
    public $record_id = false;
    public $record_title = 'RECORD';
    // back button
    public $back_button = true;
    public $delete_button = true;

    /**
     * Class Constructor
     *
     * @param   string  $str_form_name
     * @param   string  $str_form_action
     * @param   string  $str_form_method
     * @param   boolean $bool_enable_upload
     */
    public function __construct($str_form_name, $str_form_action, $str_form_method = 'post', $bool_enable_upload = true)
    {
        // execute parent constructor
        parent::__construct($str_form_name, $str_form_action, $str_form_method, $bool_enable_upload);
    }


    /**
     * Method to print out form table
     *
     * @return  string
     */
    public function printOut()
    {
        // create table object
        $_table = new simbio_table();
        // set the table attr
        $_table->table_attr = $this->table_attr;
		if ($this->edit_mode) {
			$this->disable = true;
		}

        $_buffer = '';
        // check if form tag is included
        if ($this->with_form_tag) {
            $_buffer .= $this->startForm()."\n";
        }

        // loop the form element
        $_row_num = 0;
        foreach ($this->elements as $row) {
           $_form_element = $row['element']->out();
           if ($_form_element_info = trim($row['info'])) {
               $_form_element .= '<div class="formElementInfo">'.$_form_element_info.'</div>';
           }
           // append row
           $_table->appendTableRow(array($row['label'], ':', $_form_element));
           // set the column header attr
           $_table->setCellAttr($_row_num+1, 0, 'width="20%" valign="top"'.$this->table_header_attr);
           $_table->setCellAttr($_row_num+1, 1, 'width="1%" valign="top"'.$this->table_header_attr);
           // set the form element column attr
           $_table->setCellAttr($_row_num+1, 2, 'width="79%" '.$this->table_content_attr);
           $_row_num++;
        }

        // link and buttons
        $_edit_link = '';
        $_delete_button = '';
        $_back_button = '';

		$_del_value = __('Delete Record');
        $_cancel_value = __('Cancel');

        // check if we are on edit form mode
        if ($this->edit_mode) {
            $_edit_link .= '<a href="#" class="editFormLink notAJAX">' . __('EDIT') . '</a>';
            // delete button exists if the record_id exists
            if ($this->record_id && $this->delete_button) {
                $_delete_button = '<input type="button" value="'.$_del_value.'" class="button btn btn-danger btn-delete" onclick="confSubmit(\'deleteForm\', \'' . addslashes(str_replace('{recordTitle}', $this->record_title, __('Are you sure to delete {recordTitle}?'))) . '\n' . addslashes(__('Once deleted, it can\'t be restored!')) .'\')" style="color: red; font-weight: bold;" />';
            }
            // back button
            if ($this->back_button) {
                $_back_button = '<input type="button" class="cancelButton button" value="'.$_cancel_value.'" onclick="javascript: self.history.back();" />';
            }
        }

        $_buttons = '';
        if ($this->with_form_tag) {
            $_buttons = '<table cellspacing="0" cellpadding="3" style="width: 100%;" class="editLink">'
                .'<tr><td><input type="submit" '.$this->submit_button_attr.' /> '.$_delete_button.' '.$_back_button.'</td><td align="right">'.$_edit_link.'</td>'
                .'</tr></table>'."\n";
        }
        // get the table result
        $_buffer .= $_buttons;
        $_buffer .= $_table->printTable();
        $_buffer .= $_buttons;

        // extract all hidden elements here
        foreach ($this->hidden_elements as $_hidden) {
            $_buffer .= $_hidden->out();
        }
        // update ID hidden elements
        if ($this->edit_mode AND $this->record_id) {
            // add hidden form element flag for detail editing purpose
            $_buffer .= '<input type="hidden" name="updateRecordID" value="'.$this->record_id.'" />';
        }

        // check if form tag is included
        if ($this->with_form_tag) {
            $_buffer .= $this->endForm()."\n";
        }

        if ($this->edit_mode) {
            // hidden form for deleting records
            $_buffer .= '<form action="'.$this->form_action.'" id="deleteForm" class="disabled" method="post" style="display: inline;"><input type="hidden" name="itemID" value="'.$this->record_id.'" /><input type="hidden" name="itemAction" value="true" /></form>';
        }
        // output
        return $_buffer;
    }
}
