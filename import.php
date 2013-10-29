<?php

require("../db_campaign.php");

// phpinfo();

// echo ord("é");
// exit;

if (!extension_loaded('json')) {
        dl('json.so');  
}

function encode($string) 
{ 
	$string = str_replace("&rsquo;", "'", $string);
	$string = str_replace("&lsquo;", "'", $string);
	$string = str_replace("&rdquo;", '"', $string);
	$string = str_replace("&ldquo;", '"', $string);
	// $string = str_replace("&ndash;", chr(45), $string);
	// $string = str_replace("&mdash;", chr(226), $string);
	// $string = str_replace("&aacute;", chr(195), $string);
	// $string = str_replace("&eacute;", "é", $string);
	// $string = str_replace("&iacute;", "í", $string);
	// $string = str_replace("&ntilde;", "ñ", $string);
	// $string = str_replace("&uacute;", "ú", $string);


	return $string;
}

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
		$aa = $v["Deck"];
		$aalink = $v["Link"];
		if (strtoupper($v["Type"]) == "STORY") {
			$pullquotes = array();
			$pq = array();
			if ($v["Pullquote1"] != "") array_push($pq, $v["Pullquote1"]);
			if ($v["Pullquote2"] != "") array_push($pq, $v["Pullquote2"]);
			if ($v["Pullquote3"] != "") array_push($pq, $v["Pullquote3"]);
			if ($v["Pullquote4"] != "") array_push($pq, $v["Pullquote4"]);
			foreach($pq as $kk => $vv) {
				$temp = array();
				$temp["quote"] = mb_convert_encoding(htmlentities($vv, "ENT-QUOTES"), 'HTML-ENTITIES', "auto");
				$temp["number"] = $kk;
				$temp["para"] = ($kk+1)+($kk*3);
				array_push($pullquotes, $temp);
			}
			$pullquotes = json_encode($pullquotes, JSON_FORCE_OBJECT);

			$url = $v["Link"];

			$jsonurl = $url . "/dmejson";

			$json = json_decode(file_get_contents( $jsonurl ) );
			// $content = select_elements("#contentHolder p .fullWidthImg, #contentHolder p .image-right, #contentHolder p .image-left", $html);
			

			//////////////////////////////
			// Check each paragraph tag //
			//////////////////////////////
			// $content = select_elements("#contentHolder img", $html);

			$inlineimages = array();
			foreach ($content as $kk => $vv) {
				
				//////////////////////////////////////////
				// and see if there are child images..  //
				//////////////////////////////////////////
				// foreach($vv['children'] as $kkk => $vvv) {
					// $type = $vvv['name'];
					// if ($type == "img") {
						$temp = array();
						$temp["src"] = $vv["attributes"]["src"];
						$temp["alt"] = $vv["attributes"]["alt"];
						$temp["title"] = $vv["attributes"]["title"];
						$temp["para"] = $kk*$kk;
						array_push($inlineimages, $temp);
					// }
				// }
			}
			$inlineimages = json_encode($inlineimages, JSON_FORCE_OBJECT);


			$content = select_elements("#contentHolder .contentBlock .innerShadow", $html);
			
			$body = array();
			$bodytext = "";	
			$tmp = $content[0]['children'];	
			
			foreach ($tmp as $kk => $vv) {
				$test = $vv;
			}
		}
		



		// $body = array();
		// $body = explode("\n\n", $v["Body"]);
		// $bodytext = "";

		// foreach($body as $kk => $vv) {
		// 	$bt = "<p>" . $vv . "</p>";
		// 	$bt = mb_convert_encoding($bt, "HTML-ENTITIES", "auto");
		// 	$bt = encode($bt);
		// 	$bodytext .= $bt;
		// }

		// $bodytext = mysql_real_escape_string($bodytext);

		// foreach($v as $kk => $vv) {
		// 	$v[$kk] = mb_convert_encoding($v[$kk], "HTML-ENTITIES", "auto");
		// 	$v[$kk] = encode($v[$kk]);
		// 	$v[$kk] = mysql_real_escape_string($v[$kk]);
		// }


		// print_r($v);
		// $query = "INSERT INTO `features` (`title`, `longdesc`, `description`, `img_large`, `html`, `pullquotes`) VALUES (\"$v[Deck]\", \"$v[Description]\", \"$v[Subhead]\", \"$v[Image]\", \"$bodytext\", \"$pullquotes\");";
		
		$query = "INSERT INTO `features` (`id`, `title`, `byline`, `description`, `longdesc`, `img_large`, `html`, `story_images`, `pullquotes`, `tags`) VALUES (NULL, '$v[Deck]', '$v[Author]', '$v[Subhead]', '$v[Description]', '$v[Image]', '$bodytext', '$inlineimages', '$pullquotes', '$v[Tags]');";
		echo $query;
		// $result = mysql_query($query) or die("Didn't work: " . $query);
	}
}

?>

		<? if ($query == "") { ?>
	<div id="main">
		<h1>Import a CSV file</h1>
		<form name="main" action="import.php" method="POST" enctype="multipart/form-data">
			<input type="file" name="filename" />
			<input type="submit" value="Submit" />


		</form>

	</div>
<? } ?>

