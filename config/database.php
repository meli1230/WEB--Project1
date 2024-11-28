<?php
class Database {
    //connection to database
    private $host = "localhost";
    private $db_name = "techpower";
    private $username = "root";
    private $password = "";
    public $conn; //variable that is declared but not assigned
                  //will store a value later, containing an instance of a database connection object

    public function getConnection() {
        $this->conn = null;
        try {
            //catch errors or exceptions during the connections to the database
            $this -> conn = new PDO ( //PDO = PHP Data Object
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this -> conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //PDO::ATTR_ERRMODE -> ensures that any database errors are thrown as exceptions
                //PDO::ERRMODE_EXCEPTION -> makes error handling more manageable by throwing exceptions for errors instead of silent failures
        } catch (PDOException $e) {
            echo "Connection Error: " .$e->getMessage(); //output an error message
        }
        return $this -> conn; //return the connection to the PDO instance if the connection is successful
                              //else, it will return null (which was initialized earlier)
    }
}