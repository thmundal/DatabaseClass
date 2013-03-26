<?php

// Utility functions

function pre_print_r($data, $return = false) {
    $pre = "<pre>" . print_r($data, true) . "</pre>";
    
    if($return)
        return $pre;
    
    echo $pre;
}

function arrGet(Array $array, $key, $return_value = "%throw-exception%") {
    if(!array_key_exists($key, $array))
        if($return_value == "%throw-exception%")
            throw new Exception("Could not find key ".$key." in array: " . pre_print_r($array, true));
        else
            return $return_value;
            
    return $array[$key];
}

function toInt($input) {
    if(!is_string($input))
        throw new Exception("Cannot convert input to integer, string expected - got ".gettype($input));
    return (float) $input;
}

function arrayToString(Array $input) {
    $output = "";
    foreach($input as $key) {
        $output .= $key. " ";
    }
    return $output;
}

function call(Callable $callback) {
    return call_user_func($callback);
}

?>