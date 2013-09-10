<?php

// phpinfo();

echo (string)FALSE;


require "../db_campaign.php";
include "parsecsv.lib.php";
include "selector.inc";

$fieldseparator = ",";
$lineseparator = chr( 13 );

// define( 'CSV_PATH', 'C:/wamp/www/csvfile/' );
// path where your CSV file is located

// $csv_file = CSV_PATH . "infotuts.csv"; // Name of your CSV file


/********************************/
/* Would you like to add an ampty field at the beginning of these records?
/* This is useful if you have a table with the first field being an auto_increment integer
/* and the csv file does not have such as empty field before the records.
/* Set 1 for yes and 0 for no. ATTENTION: don't set to 1 if you are not sure.
/* This can dump data in the wrong fields if this extra field does not exist in the table
/********************************/
$addauto = 0;
/********************************/

/* Would you like to save the mysql queries in a file? If yes set $save to 1.
/* Permission on the file should be set to 777. Either upload a sample file through ftp and
/* change the permissions, or execute at the prompt: touch output.sql && chmod 777 output.sql
/********************************/
$save = 0;
$outputfile = "output.sql";
/********************************/



if ( $_FILES["filename"]["size"] > 0 ) {
	$csvfile = $_FILES["filename"]["tmp_name"];
	if ( !file_exists( $csvfile ) ) {
		echo "File not found. Make sure you specified the correct path.\n";
		exit;
	}
	$file = fopen( $csvfile, "r" );
	if ( !$file ) {
		echo "Error opening data file.\n";
		exit;
	}
	$size = filesize( $csvfile );
	if ( !$size ) {
		echo "File is empty.\n";
		exit;
	}
	$csvcontent = fread( $file, $size );
	$csv = new parseCSV();
	$csv->auto( $csvcontent );
	$csvdata = array();
	$csvdata = $csv->data;



	fclose( $file );


	foreach ( $csvdata as $k => $v ) {
		// $pullquotes = "";
		$url = $v["Link"];
		$html = file_get_contents( $url );
		$content = select_elements("#contentHolder p", $html);
		foreach ($content as $kk => $vv) {
			$text = $vv["text"];
			if (strpos($a,'are') !== false) {
			    echo 'true';
			}

			$logic = strpos($text,'About Michigan Engineering: ');

			if ($text != "" && !strpos($text,'About Michigan Engineering: ')) { 
				echo mb_convert_encoding( $vv["text"], 'HTML-ENTITIES', "auto" );
				echo "<br /><br />";
			}
		}
		// print_r($v);
		// $query = "INSERT INTO `features` (`title`, `longdesc`, `description`, `img_tall`, `img_large`, `img_med`, `img_sm`, `tags`, `html`, `options`, `customStyle`, `story_images`, `pullquotes`, `weight`) VALUES (NULL, 'Test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', NULL)";

	echo "******************************<br /><br /><br />";

	}
}

?>

<html>
<head>
	<title>File upload</title>
	<link rel="stylesheet" href="css/import.css">
</head>
<body>

	<div id="main">
		<h1>Import a CSV file</h1>
		<form name="main" action="import.php" method="POST" enctype="multipart/form-data">
			<input type="file" name="filename" />
			<input type="submit" value="Submit" />


		</form>
	</div>

</body>
</html>
