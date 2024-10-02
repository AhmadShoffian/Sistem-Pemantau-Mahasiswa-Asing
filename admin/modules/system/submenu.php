<?php

/* Membership module submenu items */
// IP based access limitation
do_checkIP('smc');
do_checkIP('smc-system');

// only administrator have privileges for below menus
    $menu[] = array(('System Environment'), MWB.'system/envinfo.php', ('Information about System Environment'));
    $menu[] = array(('Modules'), MWB.'system/module.php', ('Configure Application Modules'));
    $menu[] = array(('System Users'), MWB.'system/app_user.php', ('Manage Application User'));
    $menu[] = array(('User Group'), MWB.'system/user_group.php', ('Manage Group of Application User'));