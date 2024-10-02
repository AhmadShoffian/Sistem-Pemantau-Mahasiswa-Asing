<?php

// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} elseif (INDEX_AUTH != 1) { 
    die("can not access this file directly");
}

class simbio_directory
{
    private $base_dir = './';
    public $multi_dir_tree = false;

    /**
     * Class constructor
     *
     * @param   string  $str_base_dir
     * @return  array
     */
    public function __construct($str_base_dir)
    {
        if (!file_exists($str_base_dir) OR !is_dir($str_base_dir)) {
            $error = 'Directory '.$str_base_dir.' doesn\'t exists!';
            throw new Exception($error);
        }
        $this->base_dir = self::stripTrailingSlash($str_base_dir);
    }


    /**
     * Method to get directory tree array
     *
     * @param   int     $int_max_downtree
     * @return  array
     */
    public function getDirectoryTree($int_max_downtree = 1, $str_current_dir = '')
    {
        if ($int_max_downtree < 1) {
            return;
        }
        // create directory object
        if ($str_current_dir) {
            $_dir2open = $this->base_dir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $str_current_dir);
            $_dir = dir($_dir2open);
        } else {
            $_dir = dir($this->base_dir);
        }

        $mixed_dir_tree = array();
        // loop directory content and search for directory
        $_d = 0;
        while (false !== ($_entry = $_dir->read())) {
            $_current_entry = $_entry;
            if ($str_current_dir) {
                $_current_entry = $str_current_dir.'/'.$_entry;
            }
            $_current_dir_path = $this->base_dir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $_current_entry);
            if (is_dir($_current_dir_path) AND $_entry != '.' AND $_entry != '..') {
                $mixed_dir_tree[$_current_entry] = $_current_entry;
                // check if this directory have descendant
                $_descendant = self::getDirectoryTree($int_max_downtree-1, $_current_entry);
                if (is_array($_descendant)) {
                    // we create multidimensional array
                    if ($this->multi_dir_tree) {
                        $mixed_dir_tree[$_current_entry.'_tree'] = $_descendant;
                    } else {
                        $mixed_dir_tree = array_merge($mixed_dir_tree, $_descendant);
                    }
                }
                $_d++;
            }
        }
        $_dir->close();
        return ($_d > 0)?$mixed_dir_tree:false;
    }
    
    public function getFileList()
    {
        $_dir = scandir($this->base_dir);
        $files = array();
        // loop directory content and search for directory
        $_d = 0;
        foreach ($_dir as $_content) {
            if (in_array($_content, array('.', '..'))) {
              continue;
            }
            $_current_path = $this->base_dir.DIRECTORY_SEPARATOR.$_content;
            if (is_file($_current_path)) {
              $files[] = $_content;
            }
        }
        return $files;        
    }

    /**
     * Strip trailing directory slash
     *
     * @param   string  $str_dir
     * @return  array
     */
    public static function stripTrailingSlash($str_dir)
    {
        return preg_replace("/(\/|\\\)$/i", '', $str_dir);
    }
}
