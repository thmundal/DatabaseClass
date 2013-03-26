<!DOCTYPE html>
<html>
    <body>
        Database class development

        <br />
        <br />
        <br />
        <?php
        require_once("mysql.php");

        $connection = mysql_connection::initialize(["host" => "localhost",
            "username" => "root",
            "password" => "",
            "dbname" => "test"]);

        $query = new query(["type" => "select",
            "table" => "test"]);
        
        //echo $query->getLastQueryString();
        
        $result = $connection->execute($query);
        
        $resultSet = $result->extract();
        
        pre_print_r($resultSet);
        
        //pre_print_r($connection);
        
        $connection->end();
        ?>
    </body>
</html>