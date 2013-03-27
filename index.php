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

//        $connection->execute(new query(
//            ["type" => "insert",
//             "table" => "test",
//             "data" => ["name" => "insert test3"]]));
        
//        $connection->execute(new query([
//            "type" => "update",
//            "table" => "test",
//            "data" => ["name" => "update test 2"],
//            "where" => "id = 2 OR id = 4"
//        ]));
        
        $connection->execute(new query([
            "type" => "delete",
            "table" => "test",
            "where" => "id = 1"
        ]));
        
        $query = new query(["type" => "select",
            "table" => "test"]);
        
        //echo $query->getLastQueryString();
        
        $result = $connection->execute($query);
        
        $resultSet = $result->extract();
        
        pre_print_r($resultSet);
        
        //pre_print_r($connection);
        
        $connection->end();
        ?>
        Test
    </body>
</html>