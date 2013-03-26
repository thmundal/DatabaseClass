<?php

// mySQL database handling for PHP
// MySQL Version 5.5.27
// PHP Version 5.4.7

require_once("util.php");

Class mysql_connection extends mysqli {
    private $connection;
    private $resultSet;
    private $connection_info;

    /*
   ["host" => "localhost",
    "username" => "",
    "password" => "",
    "dbname" => "",
    "port" => "",
    "socket" => ""]);*/
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    public static function initialize(Array $connection_info) {
        static $instance;
        
        if($instance == null) {
            $connection = new parent(arrGet($connection_info, "host"),
                                           arrGet($connection_info, "username"),
                                           arrGet($connection_info, "password"),
                                           arrGet($connection_info, "dbname"),
                                           toInt(arrGet($connection_info, "port", ini_get("mysqli.default_port"))),
                                           arrGet($connection_info, "socket", ini_get("mysqli.default_socket")));
            
            $instance = new self($connection);
            $instance->connection = $connection;
            $instance->connection_info = $connection_info;
        }
        return $instance;
    }
    
    public function end() {
        mysqli_close($this->connection);
    }
    
    public function execute(query $query) {
        return ($this->result = new result($this->connection, $query));
    }
}

Class query  {
    private $query_string;
    private $result;
    private $num_rows;

    public function __construct(Array $query_parameters) {
        $this->query_string = $this->{arrGet($query_parameters, "type")}($query_parameters);
    }

    public function select(Array $parameters) {
        return arrayToString(array_merge(
                                ["SELECT", call(function() use($parameters) {
                                    if(is_array($fields = arrGet($parameters, "fields", "*")))
                                        return implode($fields, ",");

                                    return $fields;
                                    }),
                                "FROM", arrGet($parameters, "table"),
                                "WHERE", arrGet($parameters, "where", "true")],
                                call(function() use($parameters) {
                                    if(($order = arrGet($parameters, "order", false)) === true)
                                        return ["ORDER BY", call(function() use($order) {
                                                                    if(is_array($order))
                                                                        return implode(",", $order);

                                                                    return $order;
                                                                })];
                                    return [];
                                })));
    }

    public function insert(Array $parameters) {

    }

    public function join(Array $parameters) {

    }

    public function getLastQueryString() {
        return $this->query_string;
    }
}

Class result extends mysql_connection {
    private $resultSet;
    
    public function __construct($connection, query $query) {
        $this->resultSet = new ResultSet($connection->query($query->getLastQueryString()));
    }

    public function extract() {
        return $this->resultSet;
    }
}

Class ResultSet {
    public function __construct($result_fields) {
        foreach($result_fields->fetch_assoc() as $row => $data) {
                $this->{$row} = $data;
        }
    }
}
?>