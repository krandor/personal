<?php 
//guestbook_setup.php
/**
 * Project: Guestbook Sample Smarty Application
 * Author: Monte Ohrt <monte [AT] ohrt [DOT] com>
 * Date: March 14th, 2005
 * File: guestbook_setup.php
 * Version: 1.0
 */

require(GUESTBOOK_DIR . 'smarty/libs/sql.lib.php');
require(GUESTBOOK_DIR . 'smarty/libs/guestbook.lib.php');
require(SMARTY_DIR . 'Smarty.class.php');
require('DB.php'); // PEAR DB

// database configuration
class GuestBook_SQL extends SQL {
    function GuestBook_SQL() {
        // dbtype://user:pass@host/dbname
        $dsn = "mysql://krandor:neophyte@localhost:3306/guestbook";
        $this->connect($dsn) || die('could not connect to database');
    }       
}

// smarty configuration
class Guestbook_Smarty extends Smarty { 
    function Guestbook_Smarty() {
        $this->template_dir = GUESTBOOK_DIR . 'smarty/templates';
        $this->compile_dir = GUESTBOOK_DIR . 'smarty/templates_c';
        $this->config_dir = GUESTBOOK_DIR . 'smarty/configs';
        $this->cache_dir = GUESTBOOK_DIR . 'smarty/cache';
    }
}      
?>