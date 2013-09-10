<?php

require "../db_campaign.php";

// define( 'CSV_PATH', 'C:/wamp/www/csvfile/' );
// path where your CSV file is located

// $csv_file = CSV_PATH . "infotuts.csv"; // Name of your CSV file

if ( $_REQUEST["filename"] != "" ) {
	$csv_file = $_REQUEST["filename"];

	$csvfile = fopen( $csv_file, 'r' );
	$theData = fgets( $csvfile );
	$i = 0;
	while ( !feof( $csvfile ) ) {
		$csv_data[] = fgets( $csvfile, 1024 );
		$csv_array = explode( ",", $csv_data[$i] );
		$insert_csv = array();
		$insert_csv['ID'] = $csv_array[0];
		$insert_csv['name'] = $csv_array[1];
		$insert_csv['email'] = $csv_array[2];


		print_r( $csv_array );

		// $query = "INSERT INTO csvdata(ID,name,email) VALUES ('','".$insert_csv['name']."','".$insert_csv['email']."')";
		// $n=mysql_query($query, $connect );
		$i++;
	}
	fclose( $csvfile );

	echo "File data successfully imported to database!!";

}

?>

<html>
<head>
	<title>File upload</title>
	<link rel="stylesheet" href="css/import.css">
</head>
<body>

	<div class="main">
		<form name="main" action="import.php" method="POST">
			<input type="file" name="filename" />
			<input type="submit" value="Submit" />


		</form>
	</div>

</body>
</html>