<?php

    if (!extension_loaded('json')) {
            dl('json.so');  
    }

    require("../db_campaign.php");

    $query = "SELECT * FROM `users`, `adminusers` WHERE id > 2000000000";
    $result = mysql_query($query);
    while($line = mysql_fetch_array($result)) {
        $myvalue = $line["name"];
		$arr = explode(' ',trim($myvalue));
		$firstname = $arr[0]; // will print Test
		echo $firstname."<br>";

		$query2 = "UPDATE `users` SET `first` = '$firstname' WHERE `id` = '$line[id]'";
		$result2 = mysql_query($query2) or die("Failed on $line[id]");
    }		


?>