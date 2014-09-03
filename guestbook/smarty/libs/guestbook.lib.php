<?php 
//guestbook.lib.php
/**
 * Project: Guestbook Sample Smarty Application
 * Author: Monte Ohrt <monte [AT] ohrt [DOT] com>
 * Date: March 14th, 2005
 * File: guestbook.lib.php
 * Version: 1.0
 */

/**
 * guestbook application library
 *
 */
class Guestbook {

    // database object
    var $sql = null;
    // smarty template object
    var $tpl = null;
    // error messages
    var $error = null;
    
    /**
     * class constructor
     */
    function Guestbook() {

        // instantiate the sql object
        $this->sql =& new GuestBook_SQL;
        // instantiate the template object
        $this->tpl =& new Guestbook_Smarty;

    }
    
    /**
     * display the guestbook entry form
     *
     * @param array $formvars the form variables
     */
    function displayForm($formvars = array()) {

        // assign the form vars
        $this->tpl->assign('post',$formvars);
        // assign error message
        $this->tpl->assign('error', $this->error);
        $this->tpl->display('guestbook_form.tpl');

    }
    
    /**
     * fix up form data if necessary
     *
     * @param array $formvars the form variables
     */
    function mungeFormData(&$formvars) {

        // trim off excess whitespace
        $formvars['Name'] = trim($formvars['Name']);
        $formvars['Comment'] = trim($formvars['Comment']);

    }

    /**
     * test if form information is valid
     *
     * @param array $formvars the form variables
     */
    function isValidForm($formvars) {

        // reset error message
        $this->error = null;
        
        // test if "Name" is empty
        if(strlen($formvars['Name']) == 0) {
            $this->error = 'name_empty';
            return false; 
        }

        // test if "Comment" is empty
        if(strlen($formvars['Comment']) == 0) {
            $this->error = 'comment_empty';
            return false; 
        }
        
        // form passed validation
        return true;
    }
    
    /**
     * add a new guestbook entry
     *
     * @param array $formvars the form variables
     */
    function addEntry($formvars) {

        $_query = sprintf(
            "insert into GUESTBOOK values(0,'%s',NOW(),'%s')",
            mysql_escape_string($formvars['Name']),
            mysql_escape_string($formvars['Comment'])
        );
        
        return $this->sql->query($_query);
        
    }
    
    /**
     * get the guestbook entries
     */
    function getEntries() {

        $this->sql->query(
            "select * from GUESTBOOK order by EntryDate DESC",
            SQL_ALL,
            SQL_ASSOC
        );

        return $this->sql->record;   
    }
    
    /**
     * display the guestbook
     *
     * @param array $data the guestbook data
     */
    function displayBook($data = array()) {

        $this->tpl->assign('data', $data);
        $this->tpl->display('guestbook.tpl');        

    }
}

?>