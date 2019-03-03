<?php
// ob_start();

  // $host = 'localhost'; //127.0.0.1
  $host = '127.0.0.1'; 
  $db_name = 'searchengine';
  $username = 'webapi';
  $password = 'aWhEXjfA8PUA6Wj';

try{
  // $con = new PDO('mysql:dbname=searchengine;host=localhost',"searcheng", "123456" );
  $con = new PDO('mysql:host=' . $host . ';dbname=' . $db_name, $username, $password);
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
  echo "Connection failed: " . $e->getMessage();
}


// class Database {
//     // DB Params
//     private $host = 'localhost';
//     private $db_name = 'myblog';
//     private $username = 'webapi';
//     private $password = 'aWhEXjfA8PUA6Wj';
//     private $conn;

//     // DB Connect
//     public function connect() {
//       $this->conn = null;

//       try { 
//         $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
//         $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//       } catch(PDOException $e) {
//         echo 'Connection Error: ' . $e->getMessage();
//       }

//       return $this->conn;
//     }
//   }

?>