<?php
/*
Plugin Name: Collection Book
Description: Controle Básico de Acervos.
Version: 1.0
Author: Edson Vieira Demetrio
*/

if (!defined('ABSPATH')) {
    exit;
}

//Register CPT and CF
require_once(plugin_dir_path(__FILE__) . 'inc/register.php');

//API CRUD
require_once(plugin_dir_path(__FILE__) . 'inc/api.php');

//Admin Page for Collections
require_once(plugin_dir_path(__FILE__) . 'admin/admin-page.php');