<?php 
	if (!extension_loaded('json')) {
			dl('json.so');  
	}
	function recordToArray($mysql_result) {
	 $rs = array();
	 while($rs[] = mysql_fetch_assoc($mysql_result)) {
	    // you don´t really need to do anything here.
	  }
	 // return json_encode($rs);
	 return $rs;
	}

	// define('UPLOADPATH', '/Applications/MAMP/htdocs/campaign/img/uploads/');
	define('UPLOADPATH', '/afs/umich.edu/group/e/engcomm/Private/uploads/img/');
	// echo (UPLOADPATH);

	if (!extension_loaded('json')) {
            dl('json.so');  
    }
    include("../db_campaign.php");
    $id = mysql_real_escape_string($_REQUEST["id"]);
	$type = mysql_real_escape_string($_REQUEST["type"]);
	$first = mysql_real_escape_string($_REQUEST["first"]);
	$last = mysql_real_escape_string($_REQUEST["last"]);
	$email = mysql_real_escape_string($_REQUEST["email"]);
	$categories = mysql_real_escape_string($_REQUEST["categories"]);
	$individuals = mysql_real_escape_string($_REQUEST["individuals"]);
	$sidebar = mysql_real_escape_string($_REQUEST["sidebar"]);
	$mailimg = mysql_real_escape_string($_REQUEST["mailimg"]);
	$msgslice = mysql_real_escape_string($_REQUEST["msgslice"]);


	if ($type == "hideviewed") {
		$query = "UPDATE `users` SET `showviewed` = '0' WHERE `id` = '$id'";
		$result = mysql_query($query) or die("Failed to update record.");
	}

	if ($type == "hidereplied") {
		$query = "UPDATE `users` SET `showviewed` = '0' WHERE `id` = '$id'";
		$result = mysql_query($query) or die("Failed to update record.");
		$query = "UPDATE `users` SET `showreplied` = '0' WHERE `id` = '$id'";
		$result = mysql_query($query) or die("Failed to update record.");
	}

	if ($type == "update") {

		if ($id > 0) {


			$set = "";

			$query = "UPDATE `users` SET";

			if ($first != "") $set .= "`first` = '$first',";
			if ($last != "") $set .= "`last` = '$last',";
			if ($first != "" && $last != "") $set .= "`name` = '$first $last',";
			if ($email != "") $set .= "`email` = '$email',";
			if ($categories != "") $set .= "`categories` = '$categories',";
			if ($individuals != "") $set .= "`individuals` = '$individuals',";
			if ($sidebar != "") $set .= "`sidebar` = '$sidebar',";
			if ($mailimg != "") $set .= "`mailimg` = '$mailimg',";
			if ($msgslice != "") $set .="`show_message_slice` = '$msgslice',";


			$set = substr($set, 0, -1);

			$set .= " WHERE `id` LIKE $id";
			$result = mysql_query($query.$set) or die("Didn't work.");
			echo "<span class='$categories' style='color: #266F26'>Changes saved.</span>";
		}
	}

	if ($type == "edit") {
		$query = "SELECT * FROM `users` WHERE id LIKE $id LIMIT 0,1";
		$result = mysql_query($query) or die("Didn't work.");
		$arr = recordToArray($result);
		header('Content-type: application/json');
		echo(json_encode($arr[0]));
	}

	if ($type == "isdifferent") {
		header('Content-type: application/json');
		$query = "SELECT * FROM `users` WHERE id LIKE $id LIMIT 0,1";
		$result = mysql_query($query) or die("Didn't work.");
		$arr = recordToArray($result);
		$msg = "";
		$return = array();
		if ($_REQUEST["first"] !== $arr[0]["first"]) {
			$msg = "<span style='color: #ff0000'>Unsaved changes!</span>";
			$return["from"] = "first";
		}
		if ($_REQUEST["last"] !== $arr[0]["last"]) {
			$msg = "<span style='color: #ff0000'>Unsaved changes!</span>";
			$return["from"] = "last";
		}
		if ($_REQUEST["email"] !== $arr[0]["email"]) {
			$msg = "<span style='color: #ff0000'>Unsaved changes!</span>";
			$return["from"] = "email";
		}
		if ($_REQUEST["categories"] !== $arr[0]["categories"]) {
			$msg = "<span style='color: #ff0000'>Unsaved changes!</span>";
			$return["from"] = "cats";
			$return["sent"] = $_REQUEST["categories"];
			$return["sfromDB"] = $arr[0]["categories"];
		}
		if ($_REQUEST["individuals"] !== $arr[0]["individuals"] && !($_REQUEST["individuals"] == '0,0,0' && $arr[0] == null)) {
			$msg = "<span style='color: #ff0000'>Unsaved changes!</span>";
			$return["from"] = "ind";
		}
		if ($_REQUEST["sidebar"] !== $arr[0]["sidebar"]) {
			$msg = "<span style='color: #ff0000'>Unsaved changes!</span>";
			$return["from"] = "sidebar";
		}
		if ($_REQUEST["msgslice"] !== $arr[0]["show_message_slice"]){
			$msg = "<span style='color: #ff0000'>Unsaved changes!</span>";
			$return["from"] = "msgslice";
		}
		
		$return['msg'] = $msg;

		echo json_encode($return);
	}

	if ($type = "sendmsg") {
		$message = mysql_real_escape_string($_REQUEST['msg']);
		$from = "a" . mysql_real_escape_string($_REQUEST['from']);
		$to = "u" . mysql_real_escape_string($_REQUEST['to']);
		$published = (int)$_REQUEST['published'];
		$img1 = mysql_real_escape_string($_REQUEST['img1']);
		$img2 = mysql_real_escape_string($_REQUEST['img2']);
		$img3 = mysql_real_escape_string($_REQUEST['img3']);
		$img4 = mysql_real_escape_string($_REQUEST['img4']);
		$img5 = mysql_real_escape_string($_REQUEST['img5']);
		$img6 = mysql_real_escape_string($_REQUEST['img6']);
		$img7 = mysql_real_escape_string($_REQUEST['img7']);
		$img8 = mysql_real_escape_string($_REQUEST['img8']);
		$img9 = mysql_real_escape_string($_REQUEST['img9']);
		$img10 = mysql_real_escape_string($_REQUEST['img10']);

		if ($_REQUEST['to'] > 0) {
			$query = "INSERT INTO `messages` (`id`, `to`, `from`, `message`, `timestamp`, `img1`, `img2`, `img3`, `img4`, `img5`, `img6`, `img7`, `img8`, `img9`, `img10`) VALUES (NULL, '$to', '$from', '$message', CURRENT_TIMESTAMP, '$img1', '$img2', '$img3', '$img4', '$img5', '$img6', '$img7', '$img8', '$img9', '$img10')";
			mysql_query($query) OR DIE("Didn't work.");
			$insertid = mysql_insert_id();
			$query = "SELECT `timestamp`, `id` FROM messages WHERE id LIKE $insertid LIMIT 0,1";
			$result = mysql_query($query);
			$line = mysql_fetch_array($result);

			if ($published == 1) echo $line["timestamp"];
			else echo $line["id"];
		}
	}

	if ($type = "sendreminder") {
		$mid = mysql_real_escape_string($_REQUEST['mid']);
		$query = "SELECT m.id as 'mid', m.reminders as 'reminders', SUBSTR(m.to, 2) AS 'to', CONCAT(u.first, ' ', u.last) as 'name', CONCAT(a.first, ' ', a.last) as 'adminname', a.email as 'adminemail', u.email as 'email' FROM `messages` as m INNER JOIN `users` AS u ON SUBSTR(m.to, 2) = u.id INNER JOIN `adminusers` as a ON SUBSTR(m.from, 2) = a.id WHERE m.id LIKE $mid";
		$result = mysql_query($query);
		while($line = mysql_fetch_array($result)) {
		    $message = "Hello $line[name]. <br /><br />$line[adminname] has sent you a new message on the Michigan Engineering Campaign platform. Please <a href='http://engcomm.engin.umich.edu/campaign'>log in</a> to view it.<br /><br />Thanks!";
			$subject = "New message for Michigan Engineering Campaign";
			$from = $line["adminname"] . "<$line[adminemail]>";
			// $headers = "From:" . $from;

			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= "From: Michigan Engineering <engcom@engin.umich.edu> \n";

			mail($line["email"],$subject,$message,$headers);
			$rem = $line["reminders"] + 2;
			echo "Send email notification (" . $rem . ")";
		}
		
		date_default_timezone_set('America/Detroit');
		$current_date = new DateTime();
		$current_date = $current_date->format('Y-m-d H:i:s');

		$query = "UPDATE `messages` SET `reminders` = `reminders` + 1, `remindertime` = '$current_date' WHERE id = $mid";
		$result = mysql_query($query);
	}

	if ($_FILES['file']['size'] > 0) {
		$tmpname = $_FILES['file']['tmp_name'];
		$filename = $_FILES['file']['name'];
		$target = UPLOADPATH . $filename;
		$moved = move_uploaded_file(($tmpname), $target);
		if( $moved ) {
		  echo "Successfully uploaded";         
		} else {
		  echo "Not uploaded";
		}
	}
?>