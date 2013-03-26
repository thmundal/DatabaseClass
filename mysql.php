<?php
/**
 * Extended MySQL handler for PHP version 5.4.7 and MySQL version 5.5.27
 * @author Thomas Mundal
 */
require_once("util.php");

Class mysql_connection extends mysqli {
    /** @var mysqli 
     * Holds the mysqli connection */
    private $connection;

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
    
    /**
     * Initializes the mysql connection
     * @staticvar self $instance
     * @param Array $connection_info
     * Contains the data used to connect to mysql via mysqli. Keynames are mandatory to the following: "username", "password", "dbname", "port" (optional), "socket" (optional)
     * The values of these corresponds to the values needed for mysqli::__construct
     * @return \self
     */
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
        }
        return $instance;
    }
    
    /**
     * Terminates the mysql connection
     */
    public function end() {
        mysqli_close($this->connection);
    }
    
    /**
     * Returns the result of a query
     * @param query $query
     * @return result
     */
    public function execute(query $query) {
        return ($this->result = new result($this->connection, $query));
    }
}

/**
 * Class for holding and handling queries and query-manipulation pre-execution
 */
Class query  {
    /**
     * Holds the generated SQL string that will eventually be evaluated trough mysql
     * @var string
     */
    private $query_string;

    /**
     * Initializes a new query and translates the parameters into usable SQL.
     * Query parameters are passed in an array by identifying information trough key/value pairs.
     * Key/value pairs that can be used are:<pre>
     * "type" => "select"/"insert"/"delete"/"update",
     * description"table" => [table_name],
     * "where" => [(string) condition] ex: "row1 = true AND row2 = \"blah\"",
     * "order" => [mysql_order_statement],
     * "limit" => [mysql_limit_statement]</pre>
     * @param Array $query_parameters
     */
    public function __construct(Array $query_parameters) {
        $this->query_string = $this->{arrGet($query_parameters, "type")}($query_parameters);
    }

    /**
     * Creates a mysql-compatible string from a given set of array key/values passed from the constructor
     * @param array $parameters
     * @return string
     */
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
    
    public function getLastQueryString() {
        return $this->query_string;
    }
}


Class result extends mysql_connection {
    /**
     *
     * @var ResultSet
     */
    private $resultSet;
    
    /**
     * 
     * @param mysqli $connection
     * @param query $query
     */
    public function __construct($connection, query $query) {
        $this->resultSet = new ResultSet($connection->query($query->getLastQueryString()));
    }

    /**
     * Extracts the resultset from the result for further manipulation
     * @return ResultSet
     */
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