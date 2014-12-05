<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of database
 *
 * @author Thomas
 * 
 * NOTE: pgsql handles duplicate connections internally
 */
class psql {
    private $connection;
    private $connection_string;
    
    public function __construct(Array $connection_info) {
        // TODO: Need to validate structure of $conneciton_info
        //"host=sheep port=5432 dbname=test user=lamb password=bar"
        $this->connection_string = "host=" . $connection_info["host"].
                                   " port=" . $connection_info["port"] .
                                   " dbname=" . $connection_info["db"] .
                                   " user=" . $connection_info["user"] .
                                   " password=" . $connection_info["password"];
        $this->connection = pg_connect($this->connection_string);
    }
    
    public function __destruct() {
        if($this->connection)
            pg_close($this->connection);
        
        $this->connection = null;
        unset($this->connection);
    }
    
    public function query($query_string, Array $params = [], $direct = true) {
        // Validate query structure... somehow..
        return new query($query_string, $this->connection, $params, $direct);
    }
    
    public function select($table, Array $fields = ['*'], $conditions = "TRUE", Array $params = [], $direct = true) {
        return $this->query("SELECT ".implode(',', $fields)." FROM ".$table." WHERE ".$conditions, $params, $this->connection, $direct);
    }
    
    public function insert($table, Array $data, $direct = true) {
        $keys = "";
        $values = "";
        foreach($data as $key => $val) {
            $keys .=$key.",";
            
            if(is_string($val))
                $val = quote($val);
            elseif($val === true)
                $val = "TRUE";
            elseif($val === false)
                $val = "FALSE";
            elseif(is_array($val))
                $val = quote(json_encode($val));
            
            $values .= $val.",";
        }
        
        return $this->query("INSERT INTO ".$table."(".substr($keys, 0, strlen($keys) -1).") VALUES(".substr($values, 0, strlen($values) -1).");", $this->connection, $direct);
        
    }
    
    public function toArray($set) {
        settype($set, 'array'); // can be called with a scalar or array
        $result = array();
        foreach ($set as $t) {
            if (is_array($t)) {
                $result[] = to_pg_array($t);
            } else {
                $t = str_replace('"', '\\"', $t); // escape double quote
               // if (! is_numeric($t)) // quote only non-numeric values
               //     $t = '"' . $t . '"';
                $result[] = $t;
            }
        }
        return '' . implode(",", $result) . ''; // format
    }
}

class query {
    public $data;
    public $rows;
    public $result;
    
    public function __construct($pg_query, $connection, $params = [], $direct = true) {
        if(!$direct)
            pg_query($connection, "BEGIN;");
        
        $this->result = pg_query_params($connection, $pg_query, $params);
        $this->rows = pg_num_rows($this->result);
    }
    
    /**
     * Fetch the data from the query as array
     * @return Array data
     */
    public function fetch() {
     $this->data = pg_fetch_array($this->result, null, PGSQL_ASSOC);
          
     return $this->data;
    }
    
    /**
     * Run a callback for each row returned by the query
     * @param callable $callback
     * @throws Exception
     */
    public function each($callback) {        
        if(!is_callable($callback))
            throw new Exception("Callback is not a function");
        
        $i=0;
        while($row = pg_fetch_array($this->result, null, PGSQL_ASSOC)) {
            $callback($row);
        }
    }
    
    public function commit($callback) {
        if(!is_callable($callback))
            throw new Exception("Callback is not a function");
     
        pg_query("COMMIT;");
        $callback($this);
    }
}