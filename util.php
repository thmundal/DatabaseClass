<?php
/**
 * Handy utility functions
 * @author Thomas Mundal <thmundal@gmail.com>
 */

/**
 * Displays or returns a datastructure printed out in HTML pre tags
 * for easier readability.
 * @param mixed $data
 * Contains the data to be printed
 * @param bool $return
 * Determens if the data is to be echoed or returned for later use
 * @return string
 * Returns the pre-formatted string, or NULL if $return is false
 */
function pre_print_r($data, $return = false) {
    $pre = "<pre>" . print_r($data, true) . "</pre>";
    
    if($return)
        return $pre;
    
    echo $pre;
    return null;
}

/**
 * Get the value of an array at the given index, if the index is not found the return value
 * is based upon the given $return_value
 * @param array $array
 * The array from which the value is to be gotten.
 * @param mixed $key
 * The key to the value to be gotten.
 * @param mixed $return_value
 * Default value is "%throw-exception%" and the function will throw an exception if the key
 * could not be found in the array and no return value is specified.
 * @return mixed
 * Returns the data at the key in the given array
 * @throws Exception
 * Exception is thrown if the given key does not exist in the array, and when a return value is not 
 * specified
 */
function arrGet(Array $array, $key, $return_value = "%throw-exception%") {
    if(!array_key_exists($key, $array))
        if($return_value == "%throw-exception%")
            throw new Exception("Could not find key ".$key." in array: " . pre_print_r($array, true));
        else
            return $return_value;
            
    return $array[$key];
}

/**
 * Converts a string to an integer value
 * @param string $input
 * @return int
 * @throws Exception
 * Throws an exception if the input value is not a string.
 */
function toInt($input) {
    if(!is_string($input))
        throw new Exception("Cannot convert input to integer, string expected - got ".gettype($input));
    return (float) $input;
}

/**
 * Converts an array to string
 * @param array $input
 * @return string
 */
function arrayToString(Array $input) {
    $output = "";
    foreach($input as $key) {
        $output .= $key. " ";
    }
    return $output;
}

/**
 * Alias function to call_user_func
 * @param Callable $callback
 * @return type
 */
function call(Callable $callback) {
    return call_user_func($callback);
}

?>