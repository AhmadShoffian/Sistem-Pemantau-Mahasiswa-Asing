<?php

// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} elseif (INDEX_AUTH != 1) {
    die("can not access this file directly");
}

include_once '../sysconfig.inc.php';

// Generate Menu
function main_menu()
{
  global $dbs;
  $modules_dir 	  = 'modules';
  $module_table   = 'mst_module';
  $module_list 	  = array();
  $_menu 	        = '';
  $icon           = array(
    'home'           => 'fa fa-home',
    'WNA'            => 'fa fa-credit-card',
    'Daftar Kendali' => 'fa fa-list-ul',
    'Pelaporan'      => 'fa fa-bar-chart',
    'Pengaturan Sistem'         => 'fa fa-cog',
    'logout'         => 'fa fa-close',

    );
  //fake, just for translation po... xieyh :(

  $appended_first  = ''; 

  $_mods_q = $dbs->query('SELECT * FROM '.$module_table.' ORDER BY `module_id` ASC ');
  while ($_mods_d = $_mods_q->fetch_assoc()) {
    $module_list[] = array('name' => $_mods_d['module_name'], 'path' => $_mods_d['module_path'], 'desc' => $_mods_d['module_desc']);
  }
  $_menu 	.= '<ul class="sidebar-menu" data-widget="tree">';
  $_menu  .= '<li class="header"></li>';
  $_menu 	.= $appended_first;
  $_menu 	.= @sub_menu('default', $module_list);
  $_menu 	.= '</li>'."\n";
  $_menu 	.= '<li><a class="menu dashboard" href="'.AWB.'index.php"><i class="nav-icon fa fa-dashboard"></i> <span class="s-menu-title">'.('Dashboard').'</span></a></li>';
  if ($module_list) {
    foreach ($module_list as $_module) {
      $_formated_module_name = ucwords(str_replace('_', ' ', $_module['name']));
      $_mod_dir = $_module['path'];
      if (isset($_SESSION['priv'][$_module['path']]['r']) && $_SESSION['priv'][$_module['path']]['r'] && file_exists($modules_dir.DS.$_mod_dir)) {
        $_icon = isset($icon[$_module['name']])?$icon[$_module['name']]:'fa fa-circle-o';
       $_menu .= '<li class="treeview"><a href="#">
            <i class="'.$_icon.'"></i>
            <span>'.($_formated_module_name).'</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a><ul class="treeview-menu">'; 
              $_menu .= sub_menu($_mod_dir, $_module);
        $_menu .= '</ul></li>';
      }
    }
  }
  $_menu .= '<li><a class="menu logout" href="logout.php"><i class="nav-icon '.$icon['logout'].'"></i> <span class="s-menu-title">' . ('Logout') . '</span></a></li>';
  $_menu .= '</ul>';
  echo $_menu;
}

function sub_menu($str_module = '', $_module = array())
{
    global $dbs;
    $modules_dir 	= 'modules';
    $_submenu 		= '<div id="sidepan"><ul class="nav">';
    $_submenu_file 	= $modules_dir.DS.$_module['path'].DS.'submenu.php';
    $menu =[];
    if (file_exists($_submenu_file)) {
        include $_submenu_file;
    } else {
       // include 'default/submenu.php';
	$shortcuts = get_shortcuts_menu();
	foreach ($shortcuts as $shortcut) {
	  $path = preg_replace('@^.+?\|/@i', '', $shortcut);
	  $label = preg_replace('@\|.+$@i', '', $shortcut);
	  //$menu[] = array(($label), MWB.$path, ($label));
	}
    }
    // iterate menu array
    foreach ($menu as $i=>$_list) {
      if ($_list[0] == 'Header') {
       $_submenu .= '<li class="header" style="color: white;background-color;background-color: #495048; font-size:10pt;">'.$menu[$i][1].'</li>';
      } else {
    $_submenu .= '<li><a class="menu s-current-child submenu-'.$i.' '.strtolower(str_replace(' ', '-', $menu[$i][0])).'" href="'.$menu[$i][1].'" title="'.( isset($menu[$i][2])?$menu[$i][2]:$menu[$i][0] ).'"><i class="fa fa-angle-double-right"></i> '.$menu[$i][0].'</a></li>'."\n";
      }
    }
    $_submenu .= '</ul></div>';
    return $_submenu;
}

function get_shortcuts_menu()
{
    global $dbs;
    $shortcuts = array();
    $shortcuts_q = $dbs->query('SELECT * FROM setting WHERE setting_name LIKE \'shortcuts_'.$dbs->escape_string($_SESSION['uid']).'\'');
    $shortcuts_d = $shortcuts_q->fetch_assoc();
    if ($shortcuts_q->num_rows > 0) {
     // $shortcuts = unserialize($shortcuts_d['setting_value']);
    }
    return $shortcuts;
}
