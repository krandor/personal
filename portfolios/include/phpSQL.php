<?php
//require_once 'config.php';
/*
	DEFINE("_HOST","localhost");
	DEFINE("_PORT","3307");
	DEFINE("_DB","sns_portfolios");
	DEFINE("_USER","highlander");
	DEFINE("_PW","0nLy1");
*/
/*********************************************************/
/*Class for connecting to a mysql DB and running queries.*/
/*********************************************************/
class phpSQL 
{
	//declare all the variables needed
	var $host=_HOST;
	var $port=_PORT;
	var $user=_USER;
	var $pass=_PW;
	var $db=_DB;
	var $conn;
	var $sql;
	var $config;
	var $fields;
	var $results;
	
	//set the query to be run
	function query($sql_statement) {
		$this->sql=$sql_statement;
	}
	
	//set the connection settings
	function set_config($host, $port, $user, $pass, $db) 
	{
		$this->host = $host;
		$this->port = $port;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
	}
	
	//get the connection settings
	function get_config() 
	{
		$this->config[0] = $this->host;
		$this->config[1] = $this->port;
		$this->config[2] = $this->user;
		$this->config[3] = $this->pass;
		$this->config[4] = $this->db;
	}
	//open the connection
	function open_conn($default,$dbvars)
	{
		//use default settings or not?
		if($default) {
		 //set config w/ default settings
		 $this->set_config($this->host,$this->port, $this->user, $this->pass, $this->db);
		 //put the configuration into an array that can be accessed later
		 $config = $this->get_config();	
		 //open the connection
		 $this->conn = mysql_connect($this->config[0].':'.$this->config[1], $this->config[2], $this->config[3]) or die ('Error Connecting to mySQL Server ('.$this->config[0].':'.$this->config[1].', '.$this->config[2].', '.$this->config[3].')');
		 //set the default database
		 mysql_select_db($this->config[4]);		 
		} else { 			
		  //set connection to a new DB
		  $this->set_config($dbvars[0],$dbvars[1],$dbvars[2],$dbvars[3],$dbvars[4]);
		  //recursive omg!
		  $this->open_conn(true,  NULL);		  
		}
		
	}
	
	function close_conn()
	{
		//close the connection for this object instance
		mysql_close($this->conn);
	}
	
	function runquery() 
	{
		//open the connection for the object
		$this->open_conn(true, NULL);
		//set the time limit php will wait before erroring
		set_time_limit(600);
		//return the results of the query
		$this->results = mysql_query($this->sql) or die('Query Failed! '.$this->sql.' <br /> ('.mysql_error().')');
		//close the connection
		$this->close_conn();
		//return the results!
		return $this->results;
    }
	
	function get_query()
	{
		return $this->sql;
	}	
	
	function get_fields()
	{
	
		for($i=0;$i<mysql_num_fields($this->results);$i++){
			$meta = mysql_fetch_field($this->results,$i);
			$this->fields[] = $meta->name;
		}		
		
		return $this->fields;
	}
}

?>