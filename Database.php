<?php


/**
* Database access class.
* Used in applications where one point of database access is required
*
* Typical Usage:
*
*Initialization :: $db = Database::getInstance();
*
*Select Query:
*
* 
* $results = $db->query("SELECT * FROM test WHERE name = :name",array(":name" => "matthew"));
* print_r($results);
*
*
*Insert Query:
*
*$db->query("insert into test(code,q_id,status) values (:code ,:q_id,:status)",
*array(":code"=>$code,":q_id"=>$row['id'],":status"=>"not_shown")
* );
*
*
*
*Update Query:
*
*$db->query('update ability_test_taking set status=:status where code=:code and q_id=:q_id'
*
*         ,array(":status"=>"taken",":code"=>$code,":q_id"=>$question_id))
*/


class Database {
 
/**
* Instance of the database class
* @static Database $instance
*/
private static $instance;
/**
* Database connection
* @access private
* @var PDO $connection
*/
private $connection;
 
/**
* Constructor
* @param $dsn The Data Source Name. eg, "mysql:dbname=testdb;host=127.0.0.1"
* @param $username
* @param $password
*/
private function __construct(){
$this->connection = new PDO("mysql:dbname=sat;host=localhost","root","");
$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
 
/**
* Gets an instance of the Database class
*
* @static
* @return Database An instance of the database singleton class.
*/
public static function getInstance(){
if(empty(self::$instance)){
try{
self::$instance = new Database();
} catch (PDOException $e) {
echo 'Connection failed: ' . $e->getMessage();
}
}
return self::$instance;
}
 
/**
* Runs a query using the current connection to the database.
*
* @param string query
* @param array $args An array of arguments for the sanitization such as array(":name" => "foo")
* @return array Containing all the remaining rows in the result set.
*/
public function query($query, $args){
$tokens = explode(" ",$query);
try{
$sth = $this->connection->prepare($query);
if(empty($args)){
$sth->execute();
}
else{
$sth->execute($args);
}
if($tokens[0] == "SELECT"){
$sth->setFetchMode(PDO::FETCH_ASSOC);
$results = $sth->fetchAll();
return $results;
}
} catch (PDOException $e) {
echo 'Query failed: ' . $e->getMessage();
echo '<br />Query : ' . $query;
}
return 1;
}
 
/**
* Returns the last inserted ID
*
* @return int ID of the last inserted row
*/
public function lastInsertId(){
return $this->connection->lastInsertId();
}

}
?>
