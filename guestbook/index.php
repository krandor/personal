<?php 
//index.php
/**
 * Project: Guestbook Sample Smarty Application
 * Author: Monte Ohrt <monte [AT] ohrt [DOT] com>
 * Date: March 14th, 2005
 * File: index.php
 * Version: 1.0
 */

// define our application directory
define('GUESTBOOK_DIR', '/var/www/guestbook/');
// define smarty lib directory
define('SMARTY_DIR', 'smarty/Smarty/');
// include the setup script
include(GUESTBOOK_DIR . 'smarty/libs/guestbook_setup.php');

// create guestbook object
$guestbook =& new Guestbook;

// set the current action
$_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

switch($_action) {
    case 'add':
        // adding a guestbook entry
        $guestbook->displayForm();
        break;
    case 'submit':
        // submitting a guestbook entry
        $guestbook->mungeFormData($_POST);
        if($guestbook->isValidForm($_POST)) {
            $guestbook->addEntry($_POST);
            $guestbook->displayBook($guestbook->getEntries());
        } else {
            $guestbook->displayForm($_POST);
        }
        break;
    case 'view':
    default:
        // viewing the guestbook
        $guestbook->displayBook($guestbook->getEntries());        
        break;   
}
?>