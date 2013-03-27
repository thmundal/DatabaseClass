<?php

    echo '<code>$connection->execute(new query([
        "type" => "update",
        "table" => "test",
        "data" => ["name" => "update test 2"],
        "where" => "id = 1"
    ]));</code>';

?>
