<?php
//require_once 'config.php';
/*
	DEFINE("_HOST","localhost");
	DEFINE("_PORT","3307");
	DEFINE("_DB","sns_portfolios");
	DEFINE("_USER","highlander");
	DEFINE("_PW","0nLy1");
	define("_eveHOST","localhost");
	define("_evePORT","3306");
	define("_eveDB","blackmes_QuantumRise");
	define("_eveUSER","blackmes_mining");
	define("_evePW",".R*S?,SNn)Ld");
*/
/*********************************************************/
/*Class for connecting to a mysql DB and running queries.*/
/*********************************************************/
class phpSQL 
{
	//declare all the variables needed
	private $host="localhost";
	private $port="3306";
	private $user="blackmes_mining";
	private $pass=".R*S?,SNn)Ld";
	private $db="blackmes_QuantumRise";
	private $conn;
	private $sql;
	private $conf;
	private $confSet = false;
	private $fields;
	private $results;
	
	//constructor
	public function __construct()
	{
		$this->conf = array($this->host, $this->port, $this->user, $this->pass, $this->db);
		$this->confSet = true;
	}
	
	//set the query to be run
	public function query($sql_statement) {
		$this->sql=$sql_statement;
	}
	
	//set the connection settings
	protected function set_config($host, $port, $user, $pass, $db) 
	{
		$this->host = $host;
		$this->port = $port;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
	}
	
	//get the connection settings
	protected function get_config() 
	{
		$this->conf[0] = $this->host;
		$this->conf[1] = $this->port;
		$this->conf[2] = $this->user;
		$this->conf[3] = $this->pass;
		$this->conf[4] = $this->db;
	}
	//open the connection
	private function open_conn($default,$dbvars)
	{
		//use default settings or not?
		if($default) {
		 //set config w/ default settings
			if(!$this->confSet)
			{
				$this->set_config($this->host,$this->port, $this->user, $this->pass, $this->db);
			}
			 //put the configuration into an array that can be accessed later
			$conf = $this->conf;	
			//open the connection
			$this->conn = mysql_connect($conf[0].':'.$conf[1], $conf[2], $conf[3]) or die ('Error Connecting to mySQL Server ('.$conf[0].':'.$conf[1].', '.$this->conf[2].')');
			//set the default database
			mysql_select_db($this->conf[4]);
		} else { 			
			//set connection to a new DB
			$this->set_config($dbvars[0],$dbvars[1],$dbvars[2],$dbvars[3],$dbvars[4]);
			//recursive omg!
			$this->open_conn(true,  NULL);		  
		}
		
	}
	
	private function close_conn()
	{
		//close the connection for this object instance
		//mysql_close($this->conn);
	}
	
	public function runquery() 
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
	
	public function get_query()
	{
		return $this->sql;
	}	
	
	public function get_fields()
	{
	
		for($i=0;$i<mysql_num_fields($this->results);$i++){
			$meta = mysql_fetch_field($this->results,$i);
			$this->fields[] = $meta->name;
		}		
		
		return $this->fields;
	}
}

?>