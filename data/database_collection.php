<?php

class DatabaseCollection { 
    public $connection;

    public function __construct($server, $host, $password, $db)
    {
        $this->connection = new mysqli($server, $host, $password, $db);
        // Check if connection was possible 
        if ($this->connection->connect_error) { 
            die("The DB cannot be connected"); 
        }
    }

    public function getError() {
        die ("Query error: " . $this->connection->error);
    }
    
    // load all articles from DB
    public function loadArticles() {
        $query = 'SELECT * FROM articles';
    
        // prepare the handle statement for later use and bind
        $statement = $this->connection->prepare($query) or  $this->getError($this->connection);
        $statement->execute() or $this->getError($this->connection);
    
        $statement_result = $statement->get_result() or  $this->getError($this->connection);
        
        $articles = [];
    
        while($row = $statement_result->fetch_assoc()) {
            $articles[$row['id']] = $row;
        }
        return $articles;
    }

    function closeConnection() { 
        $this->connection->close();
    }
}