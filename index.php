<?php
    ///////////////
    // PHP stuff //
    ///////////////
    // if ($_SERVER['REMOTE_USER'] == "") $_SERVER['REMOTE_USER'] == 'tkdman';

    if (!extension_loaded('json')) {
            dl('json.so');  
    }

    require("../db_campaign.php");

    $where = "";
    $limit = "";

    $admin = $_SERVER['REMOTE_USER'];

    $admin = ($admin == "") ? "tkdman" : $admin;

    $query = "SELECT * FROM `adminusers` WHERE `uniqname` LIKE '$admin' LIMIT 0,1";
    $result = mysql_query($query) or die("Fail: " . $query);     
    $line = mysql_fetch_array($result);

    $aid = $line['id'];

    $aname = $line['first'] . " " . $line['last'];
    
    if ($line['uids'] != "") {
        $uidArr = array();
        $uidArr = explode(",", $line['uids']);
        $where = "WHERE `id` LIKE ";
        foreach ($uidArr as $key => $value) {
            $where .= "'$value' OR `id` LIKE ";
        }
        $where = substr($where, 0, -13);
        $where .= ";";
        // echo $where;
    }

    $query = "SELECT * FROM `users`$where$limit";
    $result = mysql_query($query) or die("Fail: " . $query);   


    $query2 = "SELECT * FROM `features`";
    $result2 = mysql_query($query2);
    $existingCats = array();

    while($line2 = mysql_fetch_array($result2)) {
        $cats = explode(",", $line2["tags"]);
        foreach($cats as $k => $v) {
            if (!in_array($v, $existingCats)) {
                array_push($existingCats, $v);

            }
        }
    }

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>MGO Dashboard</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="css/normalize.min.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/admin.css">
        <link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />

        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
        <script type="text/javascript" src="//use.typekit.net/hzy7lrd.js"></script>
        <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
        <script type="text/javascript" src="js/plupload.js"></script>
        <script type="text/javascript" src="js/plupload.html4.js"></script>
        <script type="text/javascript" src="js/plupload.html5.js"></script>
        <script type="text/javascript">

            var unreadIds = Array();
            var existingCats=new Array(<? foreach($existingCats as $k => $v) {$jsec .= "\"$v\",";}$jsec = substr($jsec, 0, -1);echo $jsec; ?>);
        </script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <div class="header-container">
            <header class="wrapper clearfix">
                <h1 class="title">MGO Dashboard</h1>
               
            </header>
        </div>

        <div class="main-container">
            <div class="main wrapper clearfix">

                <article>
                 
                    <section class="userlist">
                        <h2>Top 200 Prospects</h2>

                        <ul class="users">

                    <? 
                        $i = 1;
                        while ($line = mysql_fetch_array($result)) {
                            $id = $line["id"];
                            $first = $line["first"];
                            $last = $line["last"];
                            $email = $line["email"];
                            $categories = $line["categories"];
                            $individuals = $line["individuals"];
                            $sidebar = $line["sidebar"];
                            $facebook = $line["password"] == "" ? true : false;
                            $cats = array();
                            $cats = explode(",", $categories);
                            $inds = array();
                            $inds = explode(",", $individuals);
							$msgslice = $line["show_message_slice"];

                    ?>
                        
                            <li class="users" id="<?= $id?>"><a class="user" href="#" data-id="<?= $id ?>" data-type="edit"><?= $first." ".$last?></a>
                                <div class="msg"><? 
                                    if ($line['showreplied'] == 1) { ?>
                                    <span style='color: #866DAB'>Prospect has replied to message [<a href="#" class="hidereplied">x</a>]</span><?}
                                    else if ($line['showviewed'] == 1) { ?>
                                    <span style='color: #866DAB'>Prospect has viewed a message [<a href="#" class="hideviewed">x</a>]</span>
                                <? }
                                ?></div>
                                <form id="<?= $id?>" enctype="multipart/form-data">
                                    <h2>Editing <?= $first." ".$last?></h2>
                                    <input type="hidden" name="id" value="<?= $id?>">

                                    <div class="first label">First Name</div>
                                    <div class="first"><?= $first?></div>

                                    <div class="last label">Last Name</div>
                                    <div class="last"><?= $last?></div>

                                    <div class="email label">Email</div>
                                    <div class="email"><?= $email?></div>

                                    <div class="facebook label">Logging in with</div>
                                    <div class="fb"><?= $facebook ? "<a target='_blank' href='http://www.facebook.com/$id'>Facebook</a>" : "Username/Password" ?></div>

                                    <h2 class="cats">Categories</h2>
                                    
                                    <div class="masonry<?= $i?>">

                                        <ul class="topic">
                                            <h3><a href="#" class="subtopic">Transportation Innovations</a></h3> 
                                            <li class="selectall">(<a href="#">Select all</a>)</li>
                                            <li><input type="checkbox" name="vautonomous" value="vautonomous"<? if (in_array("vautonomous", $cats)) echo " checked";?><? if (in_array("vautonomous", $cats)) echo " checked";?>><label class="vautonomous">Autonomous Vehicles</label></li>
                                            <li><input type="checkbox" name="disdriving" value="disdriving"<? if (in_array("disdriving", $cats)) echo " checked";?>><label for="disdriving">Distracted Driving</label></li>
                                            <li><input type="checkbox" name="invehicletech" value="invehicletech"<? if (in_array("invehicletech", $cats)) echo " checked";?>><label for="invehicletech">In-Vehicle Technology</label></li>
                                            <li><input type="checkbox" name="vehiclesafety" value="vehiclesafety"<? if (in_array("vehiclesafety", $cats)) echo " checked";?>><label for="vehiclesafety">Vehicle Safety</label></li>
                                            <li><input type="checkbox" name="preventingaccidents" value="preventingaccidents"<? if (in_array("preventingaccidents", $cats)) echo " checked";?>><label for="preventingaccidents">Preventing Accidents</label></li>
                                            <li><input type="checkbox" name="ivcommunication" value="ivcommunication"<? if (in_array("ivcommunication", $cats)) echo " checked";?>><label for="ivcommunication">Inter-Vehicle Communication</label></li>
                                            <li><input type="checkbox" name="apps" value="apps"<? if (in_array("apps", $cats)) echo " checked";?>><label for="apps">Apps</label></li>
                                            <li><input type="checkbox" name="hybridenergy" value="hybridenergy"<? if (in_array("hybridenergy", $cats)) echo " checked";?>><label for="hybridenergy">Hybrid Energy</label></li>
                                            <li><input type="checkbox" name="biofuels" value="biofuels"<? if (in_array("biofuels", $cats)) echo " checked";?>><label for="biofuels">Biofuels</label></li>
                                            <li><input type="checkbox" name="batteries" value="batteries"<? if (in_array("batteries", $cats)) echo " checked";?>><label for="batteries">Batteries</label></li>
                                            <li><input type="checkbox" name="supercap" value="supercap"<? if (in_array("supercap", $cats)) echo " checked";?>><label for="supercap">Super Capacitors</label></li>                                        
                                            <li><input type="checkbox" name="fuelefficiency" value="fuelefficiency"<? if (in_array("fuelefficiency", $cats)) echo " checked";?>><label for="fuelefficiency">Fuel Efficiency</label></li>
                                            <li><input type="checkbox" name="lwvehicles" value="lwvehicles"<? if (in_array("lwvehicles", $cats)) echo " checked";?>><label for="lwvehicles">Lightweight Vehicles</label></li>
                                            <li><input type="checkbox" name="aerodynamics" value="aerodynamics"<? if (in_array("aerodynamics", $cats)) echo " checked";?>><label for="aerodynamics">Aerodynamics</label></li>
                                        </ul>

                                        <ul class="topic">
                                            <h3><a href="#" class="subtopic">Securing our Future</a></h3>
                                            <li class="selectall">(<a href="#">Select all</a>)</li>
                                            <li><input type="checkbox" name="mcubed" value="weaponsdetection"<? if (in_array("weaponsdetection", $cats)) echo " checked";?>><label for="mcubed">Weapons Detection</label></li>
                                            <li><input type="checkbox" name="nuclearnon" value="nuclearnon"<? if (in_array("nuclearnon", $cats)) echo " checked";?>><label for="nuclearnon">Nuclear Non-Proliferation</label></li>
                                            <li><input type="checkbox" name="drones" value="drones"<? if (in_array("drones", $cats)) echo " checked";?>><label for="drones">Drones</label></li>
                                            <li><input type="checkbox" name="autonomous" value="autonomous"<? if (in_array("autonomous", $cats)) echo " checked";?>><label for="autonomous">Autonomous Systems</label></li>
                                            <li><input type="checkbox" name="cybersec" value="cybersec"<? if (in_array("cybersec", $cats)) echo " checked";?>><label for="cybersec">CyberSecurity</label></li>
                                            <li><input type="checkbox" name="surveillance" value="surveillance"<? if (in_array("surveillance", $cats)) echo " checked";?>><label for="surveillance">Surveillance</label></li>
                                            <li><input type="checkbox" name="nuclear" value="nuclear"<? if (in_array("nuclear", $cats)) echo " checked";?>><label for="nuclear">Nuclear</label></li>
                                            <li><input type="checkbox" name="natsec" value="natsec"<? if (in_array("natsec", $cats)) echo " checked";?>><label for="natsec">National Security</label></li>
                                            <li><input type="checkbox" name="millitary" value="millitary"<? if (in_array("millitary", $cats)) echo " checked";?>><label for="millitary">Military Applications</label></li>
                                            <li><input type="checkbox" name="infrastructure" value="infrastructure"<? if (in_array("infrastructure", $cats)) echo " checked";?>><label for="infrastructure">Infrastructure</label></li>
                                            <li><input type="checkbox" name="disaster" value="disaster"<? if (in_array("disaster", $cats)) echo " checked";?>><label for="disaster">Disaster Preparedness</label></li>
                                            <li><input type="checkbox" name="weather" value="weather"<? if (in_array("weather", $cats)) echo " checked";?>><label for="weather">Weather Prediction</label></li>
                                        </ul>    

                                        <ul class="topic">
                                            <h3><a href="#" class="subtopic">Revolutionary Materials</a></h3>
                                            <li class="selectall">(<a href="#">Select all</a>)</li>
                                            <li><input type="checkbox" name="nanotech" value="nanotech"<? if (in_array("nanotech", $cats)) echo " checked";?>><label for="nanotech">Nanotechnology</label></li>
                                            <li><input type="checkbox" name="composites" value="composites"<? if (in_array("composites", $cats)) echo " checked";?>><label for="composites">Composites</label></li>
                                            <li><input type="checkbox" name="polymers" value="polymers"<? if (in_array("polymers", $cats)) echo " checked";?>><label for="polymers">Polymers</label></li>
                                            <li><input type="checkbox" name="lightweightmat" value="lightweightmat"<? if (in_array("lightweightmat", $cats)) echo " checked";?>><label for="lightweightmat">Lightweight Materials</label></li>
                                            <li><input type="checkbox" name="othermaterials" value="othermaterials"<? if (in_array("othermaterials", $cats)) echo " checked";?>><label for="othermaterials">Other Materials</label></li>
                                            <li><input type="checkbox" name="peptides" value="peptides"<? if (in_array("peptides", $cats)) echo " checked";?>><label for="peptides">Peptides</label></li>      
                                            <li><input type="checkbox" name="drugdelivery" value="drugdelivery"<? if (in_array("drugdelivery", $cats)) echo " checked";?>><label for="drugdelivery">Drug Delivery</label></li>
                                            <li><input type="checkbox" name="safety" value="safety"<? if (in_array("safety", $cats)) echo " checked";?>><label for="safety">Safety</label></li>
                                            <li><input type="checkbox" name="solarcells" value="solarcells"<? if (in_array("solarcells", $cats)) echo " checked";?>><label for="solarcells">Solar Cells</label></li>
                                            <li><input type="checkbox" name="gels" value="gels"<? if (in_array("gels", $cats)) echo " checked";?>><label for="gels">Gels</label></li>
                                            <li><input type="checkbox" name="memorymetals" value="memorymetals"<? if (in_array("memorymetals", $cats)) echo " checked";?>><label for="memorymetals">Memory Metals</label></li>
                                            <li><input type="checkbox" name="electronics" value="electronics"<? if (in_array("electronics", $cats)) echo " checked";?>><label for="electronics">Electronics</label></li>
                                            <li><input type="checkbox" name="computersfuture" value="computersfuture"<? if (in_array("computersfuture", $cats)) echo " checked";?>><label for="computersfuture">Computers of the Future</label></li>
                                            <li><input type="checkbox" name="environment" value="environment"<? if (in_array("environment", $cats)) echo " checked";?>><label for="environment">Environment</label></li>
                                        </ul>

                                        <ul class="topic">
                                            <h3><a href="#" class="subtopic">Healthcare</a></h3>
                                            <li class="selectall">(<a href="#">Select all</a>)</li>
                                            <li><input type="checkbox" name="disresearch" value="disresearch"<? if (in_array("disresearch", $cats)) echo " checked";?>><label for="disresearch">Disease Research</label></li>
                                            <li><input type="checkbox" name="distreatment" value="distreatment"<? if (in_array("distreatment", $cats)) echo " checked";?>><label for="distreatment">Disease Treatment</label></li>
                                            <li><input type="checkbox" name="drugs" value="drugs"<? if (in_array("drugs", $cats)) echo " checked";?>><label for="drugs">Drugs</label></li>
                                            <li><input type="checkbox" name="drugdelivery" value="drugdelivery"<? if (in_array("drugdelivery", $cats)) echo " checked";?>><label for="drugdelivery">Drug Delivery</label></li>
                                            <li><input type="checkbox" name="3dprinting" value="3dprinting"<? if (in_array("3dprinting", $cats)) echo " checked";?>><label for="3dprinting">3D Printing</label></li>
                                            <li><input type="checkbox" name="imaging" value="imaging"<? if (in_array("imaging", $cats)) echo " checked";?>><label for="imaging">Imaging</label></li>      
                                            <li><input type="checkbox" name="diagnostics" value="diagnostics"<? if (in_array("diagnostics", $cats)) echo " checked";?>><label for="diagnostics">Diagnostics</label></li>
                                            <li><input type="checkbox" name="meddevices" value="meddevices"<? if (in_array("meddevices", $cats)) echo " checked";?>><label for="meddevices">Medical Devices</label></li>
                                            <li><input type="checkbox" name="tissengineering" value="tissengineering"<? if (in_array("tissengineering", $cats)) echo " checked";?>><label for="tissengineering">Tissue Engineering</label></li>
                                            <li><input type="checkbox" name="patientcare" value="patientcare"<? if (in_array("patientcare", $cats)) echo " checked";?>><label for="patientcare">Patient Care</label></li>
                                            <li><input type="checkbox" name="affordability" value="affordability"<? if (in_array("affordability", $cats)) echo " checked";?>><label for="affordability">Affordability</label></li>
                                            <li><input type="checkbox" name="safety" value="safety"<? if (in_array("safety", $cats)) echo " checked";?>><label for="safety">Safety</label></li>
                                            <li><input type="checkbox" name="quality" value="quality"<? if (in_array("quality", $cats)) echo " checked";?>><label for="quality">Quality</label></li>
                                            <li><input type="checkbox" name="therapies" value="therapies"<? if (in_array("therapies", $cats)) echo " checked";?>><label for="therapies">Therapies</label></li>
                                            <li><input type="checkbox" name="globalhealth" value="globalhealth"<? if (in_array("globalhealth", $cats)) echo " checked";?>><label for="globalhealth">Global Health</label></li>
                                            <li><input type="checkbox" name="apptechnologies" value="apptechnologies"<? if (in_array("apptechnologies", $cats)) echo " checked";?>><label for="apptechnologies">Appropriate Technologies</label></li>
                                        </ul>

                                        <ul class="topic">
                                            <h3><a href="#" class="subtopic">Economics & Entrepreneurship</a></h3>
                                            <li class="selectall">(<a href="#">Select all</a>)</li>
                                            <li><input type="checkbox" name="mcubed" value="mcubed"<? if (in_array("mcubed", $cats)) echo " checked";?>><label for="mcubed">MCubed</label></li>
                                            <li><input type="checkbox" name="techtransfer" value="techtransfer"<? if (in_array("techtransfer", $cats)) echo " checked";?>><label for="techtransfer">Office of Tech Transfer</label></li>
                                            <li><input type="checkbox" name="studentstart" value="studentstart"<? if (in_array("studentstart", $cats)) echo " checked";?>><label for="studentstart">Startups – Student</label></li>
                                            <li><input type="checkbox" name="facultystart" value="facultystart"<? if (in_array("facultystart", $cats)) echo " checked";?>><label for="facultystart">Startups – Faculty</label></li>
                                            <li><input type="checkbox" name="cfe" value="cfe"<? if (in_array("cfe", $cats)) echo " checked";?>><label for="cfe">Center for Entrepreneurship</label></li>
                                            <li><input type="checkbox" name="me" value="me"<? if (in_array("me", $cats)) echo " checked";?>><label for="me">Masters of Entrepreneurship</label></li>      
                                            <li><input type="checkbox" name="indcollab" value="indcollab"<? if (in_array("indcollab", $cats)) echo " checked";?>><label for="indcollab">Industry Collaborations</label></li>
                                            <li><input type="checkbox" name="innovation" value="innovation"<? if (in_array("innovation", $cats)) echo " checked";?>><label for="innovation">Innovation</label></li>
                                            <li><input type="checkbox" name="economy" value="economy"<? if (in_array("economy", $cats)) echo " checked";?>><label for="economy">Economy</label></li>
                                        </ul>

                                        <ul class="topic">
                                            <h3><a href="#" class="subtopic">Global Resources</a></h3>
                                            <li class="selectall">(<a href="#">Select all</a>)</li>
                                            <li><input type="checkbox" name="sustainability" value="sustainability"<? if (in_array("sustainability", $cats)) echo " checked";?>><label for="sustainability">Sustainability</label></li>
                                            <li><input type="checkbox" name="cleanwater" value="cleanwater"<? if (in_array("cleanwater", $cats)) echo " checked";?>><label for="cleanwater">Clean Water</label></li>
                                            <li><input type="checkbox" name="watershortage" value="watershortage"<? if (in_array("watershortage", $cats)) echo " checked";?>><label for="watershortage">Water Shortage</label></li>
                                            <li><input type="checkbox" name="waterpurification" value="waterpurification"<? if (in_array("waterpurification", $cats)) echo " checked";?>><label for="waterpurification">Water Purification</label></li>
                                            <li><input type="checkbox" name="globalenergy" value="globalenergy"<? if (in_array("globalenergy", $cats)) echo " checked";?>><label for="globalenergy">Global Energy</label></li>
                                            <li><input type="checkbox" name="eefficiency" value="eefficiency"<? if (in_array("eefficiency", $cats)) echo " checked";?>><label for="eefficiency">Energy Efficiency</label></li>      
                                            <!-- <li><input type="checkbox" name="biofuels" value="biofuels"<? if (in_array("biofuels", $cats)) echo " checked";?>><label for="biofuels">Biofuels</label></li> -->
                                            <li><input type="checkbox" name="batteries" value="meddevices"<? if (in_array("meddevices", $cats)) echo " checked";?>><label for="meddevices">Batteries</label></li>
                                            <li><input type="checkbox" name="solarpower" value="solarpower"<? if (in_array("solarpower", $cats)) echo " checked";?>><label for="solarpower">Solar Power</label></li>
                                            <li><input type="checkbox" name="hydropower" value="hydropower"<? if (in_array("hydropower", $cats)) echo " checked";?>><label for="hydropower">Hydropower</label></li>
                                            <li><input type="checkbox" name="windpower" value="windpower"<? if (in_array("windpower", $cats)) echo " checked";?>><label for="windpower">Wind Power</label></li>
                                            <li><input type="checkbox" name="geothermalpower" value="geothermalpower"<? if (in_array("geothermalpower", $cats)) echo " checked";?>><label for="geothermalpower">Goethermal Power</label></li>
                                            <li><input type="checkbox" name="nuclearpower" value="nuclearpower"<? if (in_array("nuclearpower", $cats)) echo " checked";?>><label for="nuclearpower">Nuclear Power</label></li>
                                            <li><input type="checkbox" name="otheresources" value="otheresources"<? if (in_array("otheresources", $cats)) echo " checked";?>><label for="otheresources">Other Energy Sources</label></li>
                                            <li><input type="checkbox" name="climatechange" value="climatechange"<? if (in_array("climatechange", $cats)) echo " checked";?>><label for="climatechange">Climate Change</label></li>
                                            <li><input type="checkbox" name="cleanair" value="cleanair"<? if (in_array("cleanair", $cats)) echo " checked";?>><label for="cleanair">Clean Air</label></li>
                                            <li><input type="checkbox" name="resourcemanagement" value="resourcemanagement"<? if (in_array("resourcemanagement", $cats)) echo " checked";?>><label for="resourcemanagement">Resource Management</label></li>
                                        </ul>

                                        <ul class="topic">
                                            <h3><a href="#" class="subtopic">Wolverine Experience</a></h3>
                                            <li class="selectall">(<a href="#">Select all</a>)</li>
                                            <li><input type="checkbox" name="scholarships" value="scholarships"<? if (in_array("scholarships", $cats)) echo " checked";?>><label for="scholarships">Scholarships</label></li>
                                            <li><input type="checkbox" name="studentteams" value="studentteams"<? if (in_array("studentteams", $cats)) echo " checked";?>><label for="studentteams">Student Teams</label></li>
                                            <li><input type="checkbox" name="classfuture" value="classfuture"<? if (in_array("classfuture", $cats)) echo " checked";?>><label for="classfuture">Classroom of the Future</label></li>
                                            <li><input type="checkbox" name="onlinelearning" value="onlinelearning"<? if (in_array("onlinelearning", $cats)) echo " checked";?>><label for="onlinelearning">Online Learning</label></li>
                                            <li><input type="checkbox" name="honors" value="honors"<? if (in_array("honors", $cats)) echo " checked";?>><label for="honors">Honors Program</label></li>
                                            <li><input type="checkbox" name="globalexp" value="globalexp"<? if (in_array("globalexp", $cats)) echo " checked";?>><label for="globalexp">Global Experience</label></li>
                                            <li><input type="checkbox" name="commoutreach" value="commoutreach"<? if (in_array("commoutreach", $cats)) echo " checked";?>><label for="commoutreach">Community Outreach (on and off-campus)</label></li>
                                            <li><input type="checkbox" name="highlevstudentprojects" value="highlevstudentprojects"<? if (in_array("highlevstudentprojects", $cats)) echo " checked";?>><label for="highlevstudentprojects">High-level Student Projects</label></li>
                                            <li><input type="checkbox" name="studentresearch" value="studentresearch"<? if (in_array("studentresearch", $cats)) echo " checked";?>><label for="studentresearch">Student Research</label></li>
                                            <li><input type="checkbox" name="gradexperience" value="gradexperience"<? if (in_array("gradexperience", $cats)) echo " checked";?>><label for="gradexperience">Grad Student Experience</label></li>
                                            <li><input type="checkbox" name="hoexperience" value="hoexperience"<? if (in_array("hoexperience", $cats)) echo " checked";?>><label for="hoexperience">Hands-on Experience</label></li>
                                            <li><input type="checkbox" name="multidisc" value="multidisc"<? if (in_array("multidisc", $cats)) echo " checked";?>><label for="multidisc">Multidisciplinary Efforts</label></li>
                                            <li><input type="checkbox" name="teams" value="teams"<? if (in_array("teams", $cats)) echo " checked";?>><label for="teams">Teams</label></li>
                                            <li><input type="checkbox" name="extracurr" value="extracurr"<? if (in_array("extracurr", $cats)) echo " checked";?>><label for="extracurr">Extracurricular</label></li>
                                            <li><input type="checkbox" name="studentstories" value="studentstories"<? if (in_array("studentstories", $cats)) echo " checked";?>><label for="studentstories">Student Stories</label></li>
                                            <li><input type="checkbox" name="nostalgia" value="nostalgia"<? if (in_array("nostalgia", $cats)) echo " checked";?>><label for="nostalgia">Nostalgia & Pride</label></li>
                                            <li><input type="checkbox" name="lifeinaa" value="lifeinaa"<? if (in_array("lifeinaa", $cats)) echo " checked";?>><label for="lifeinaa">Life in Ann Arbor</label></li>
                                        </ul>
                                    </div>

                                    <input type="hidden" name="categories" value="<?= $categories?>">

                                    <h2>Other</h2>

                                    <label for="sidebar">"Welcome" sidebar brief greeting</label>
                                    <!--input type="text" name="sidebar" value="<?= $sidebar?>"-->

                                    <textarea name="sidebar"><?= $sidebar ?></textarea>
                                    <br>
                                    <br>


                                    <label for="ind1">Override spot one:</label>
                                    <select class="individual" name="ind1">
                                        <option value="0"></option>
                                        <?
                                        $query3 = "SELECT * FROM `features`";
                                        $result3 = mysql_query($query3);
                                        while($line3 = mysql_fetch_array($result3)) { ?>
                                         
                                         <option value="<?= $line3['id']?>"<? if ($inds[0] == $line3['id']) echo " selected"; ?>><?= $line3['title']?></option>

                                       <?  

                                   }


                                        ?>
                                    </select> <br />

                                    <label for="ind2">Override spot two:</label>
                                    <select class="individual" name="ind2">
                                        <option value="0"></option>
                                        <?
                                        $query3 = "SELECT * FROM `features`";
                                        $result3 = mysql_query($query3);
                                        while($line3 = mysql_fetch_array($result3)) { ?>
                                         
                                         <option value="<?= $line3['id']?>"<? if ($inds[1] == $line3['id']) echo " selected"; ?>><?= $line3['title']?></option>

                                       <?  }


                                        ?>
                                    </select> <br />

                                    <label for="ind3">Override spot three:</label>
                                    <select class="individual" name="ind3">
                                        <option value="0"></option>
                                        <?
                                        $query3 = "SELECT * FROM `features`";
                                        $result3 = mysql_query($query3);
                                        while($line3 = mysql_fetch_array($result3)) { ?>
                                         
                                         <option value="<?= $line3['id']?>"<? if ($inds[2] == $line3['id']) echo " selected"; ?>><?= $line3['title']?></option>

                                       <?  }


                                        ?>
                                    </select> <br />
									<label>Message Slice</label><input type="checkbox" name="msgslice" <? if($msgslice==='0') echo "checked value='0'" ?>><label style="float:none;"> Hide the message slice.</label><br />

                                    <input type="hidden" name="individuals" value="<?= $individuals?>"><br>


                                    <div class="msgbg">
                                        <h2>Choose messaging background image <?= $line['mailimg']?></h2>

                                        <ul class="bgimages">
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail.jpg') echo ' active'; ?>" id="mail"><img src="img/mail_sm/mail.jpg" alt="Background image option" /></a></li>
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail2.jpg') echo ' active'; ?>" id="mail2"><img src="img/mail_sm/mail2.jpg" alt="Background image option" /></a></li>
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail3.jpg') echo ' active'; ?>" id="mail3"><img src="img/mail_sm/mail3.jpg" alt="Background image option" /></a></li>
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail4.jpg') echo ' active'; ?>" id="mail4"><img src="img/mail_sm/mail4.jpg" alt="Background image option" /></a></li>
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail5.jpg') echo ' active'; ?>" id="mail5"><img src="img/mail_sm/mail5.jpg" alt="Background image option" /></a></li>
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail6.jpg') echo ' active'; ?>" id="mail6"><img src="img/mail_sm/mail6.jpg" alt="Background image option" /></a></li>
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail7.jpg') echo ' active'; ?>" id="mail7"><img src="img/mail_sm/mail7.jpg" alt="Background image option" /></a></li>
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail8.jpg') echo ' active'; ?>" id="mail8"><img src="img/mail_sm/mail8.jpg" alt="Background image option" /></a></li>
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail9.jpg') echo ' active'; ?>" id="mail9"><img src="img/mail_sm/mail9.jpg" alt="Background image option" /></a></li>
                                            <li><a href="#" class="imagechoice<? if ($line['mailimg'] == 'mail910.jpg') echo ' active'; ?>" id="mail10"><img src="img/mail_sm/mail10.jpg" alt="Background image option" /></a></li>
                                            <input type="hidden" name="mailimg" value="<?= $line['mailimg']?>" />
                                        </ul>
                                    </div>


                                    <h2>Messages to prospect</h2>

                                    <div class="messages">
                                    
                                    <? 
                                        $query2 = "SELECT m.id as mid, SUBSTR(m.from, 2) AS 'from', SUBSTR(m.to, 2) AS 'to', m.message, m.timestamp, m.img1, m.img2, m.img3, m.img4, m.img5, m.img6, m.img7, m.img8, m.img9, m.img10, u.id, CONCAT(a.first, ' ', a.last) AS fromName, CONCAT(u.first, ' ', u.last) AS toName, m.reminders as reminders, m.remindertime FROM `messages` AS m INNER JOIN `users` AS u ON SUBSTR(m.to, 2) = u.id INNER JOIN `adminusers` AS a ON SUBSTR(m.from, 2) = a.id WHERE SUBSTR(m.to, 2) LIKE '$id' AND SUBSTR(m.from, 2) LIKE '$aid' UNION SELECT m.id, SUBSTR(m.from, 2) AS 'from', SUBSTR(m.to, 2), m.message, m.timestamp, m.img1, m.img2, m.img3, m.img4, m.img5, m.img6, m.img7, m.img8, m.img9, m.img10, u.id, CONCAT(u.first, ' ', u.last), CONCAT(a.first, ' ', a.last), m.reminders as reminders, m.remindertime FROM `messages` AS m INNER JOIN `users` AS u ON SUBSTR(m.from, 2) = u.id INNER JOIN `adminusers` AS a ON SUBSTR(m.to, 2) = a.id WHERE SUBSTR(m.from, 2) LIKE '$id' AND SUBSTR(m.to, 2) LIKE '$aid' ORDER BY `timestamp` ASC";

                                        $unread = "";
                                        $result2 = mysql_query($query2);
                                        $hasimages = false;

                                        if (mysql_num_rows($result2) == 0) {
                                            echo "<div class=\"message\"></div>";
                                        }

                                        while ($line2 = mysql_fetch_array($result2)) { 
                                        
                                        // if ($line2['mid'] == 2) xdebug_break();

                                        $images = array(
                                            1 => $line2["img1"], 
                                            2 => $line2["img2"],
                                            3 => $line2["img3"],
                                            4 => $line2["img4"],
                                            5 => $line2["img5"],
                                            6 => $line2["img6"],
                                            7 => $line2["img7"],
                                            8 => $line2["img8"],
                                            9 => $line2["img9"],
                                            10 => $line2["img10"]
                                        );

                                        $imgurls = array();
                                        foreach($images as $k => $v) {
                                            if ($v != null) {
                                                $hasimages = true;
                                                $imgurls[] = $v;
                                            }
                                        }
										
										// check the time interval
										if(isset($line2['remindertime'])&&$line2['remindertime']!=NULL&&$line2['remindertime']!=''){
											date_default_timezone_set('America/Detroit');
											$current_date = new DateTime();
											$last_reminder = DateTime::createFromFormat('Y-m-d H:i:s',$line2["remindertime"]);
											$interval = $current_date->diff($last_reminder)->days;
										}else{
											$interval = 1;
										}
										
										
                                        ?>

                                        <div class="message">
                                            <h3 class="from"><?= $line2["fromName"]. " " . $line2["last"]?></h3>
                                            <span class="timestamp"><?= $line2["timestamp"]?></span>
                                            <?= $line2["message"] ?>
                                            <? if ($hasimages) { ?>
                                                <p class="attachments">This message has attachments. <a target="_blank" href="preview.php?id=<?= $line2['mid']?>">Preview it.</a></p>
                                            <? } $hasimages = false;?>

                                            <? if ($line2["from"] == $aid) { ?>
												<?if ($interval > 0) {?> 
                                            		<span class="sendemail">(<a href="#" data-mid="<?= $line2['mid']?>">Send email notification (<?= $line2["reminders"] + 1?>)</a>)</span>
												<?}else{?>
													<span class="sendemail">Reminder sent. Please wait 24 hours before sending again.</span>
												<? }?>
                                            <? } ?>

                                        </div>


                                    <? } ?>

                                        <div class="sendmessage">
                                            <textarea class="sendmsg"></textarea>
                                            <!-- <div class="imgup"><label class="file" for="file1">Image 1:</label><input name="file1" type="file"></div>
                                            <div class="imgup"><label class="file" for="file2">Image 2:</label><input name="file2" type="file"></div>
                                            <div class="imgup"><label class="file" for="file3">Image 3:</label><input name="file3" type="file"></div>
                                            <div class="imgup"><label class="file" for="file4">Image 4:</label><input name="file4" type="file"></div>
                                            <div class="imgup"><label class="file" for="file5">Image 5:</label><input name="file5" type="file"></div>
                                            <div class="imgup"><label class="file" for="file6">Image 5:</label><input name="file6" type="file"></div>
                                            <div class="imgup"><label class="file" for="file7">Image 5:</label><input name="file7" type="file"></div>
                                            <div class="imgup"><label class="file" for="file8">Image 5:</label><input name="file8" type="file"></div>
                                            <div class="imgup"><label class="file" for="file9">Image 5:</label><input name="file9" type="file"></div>
                                            <div class="imgup"><label class="file" for="file10">Image 5:</label><input name="file10" type="file"></div> -->


                                            <div class="filecontainer" id="<?= $id?>">
                                                <div class="filelist">No runtime found.</div>
                                                <br />
                                                <a id="pickfiles<?= $id?>" href="javascript:;">[Select files]</a> 
                                                <a class="uploadfiles" href="javascript:;">[Upload files]</a>
                                            </div>


                                            <input class="sendmsg" type="submit" data-from="<?= $aid ?>" data-to="<?= $id ?>" data-fromname="<?= $aname?>" data-publish="1" value="Send Message" />
                                            <a href="#" class="preview" data-from="<?= $aid ?>" data-to="<?= $id ?>" data-fromname="<?= $aname?>" data-publish="0">[ Preview message ]</a>
                                        </div>

                                    </div>
                                    <br>


                                    <input class="submit" type="submit" value="Save changes">
                                    <a class="close" href="#">Close</a>

                                    

                                </form>
                            </li>
                   <?
                            $i++;

                        }
                   ?>

                        </ul>
                    </section>

         
                </article>

            </div> <!-- #main -->
        </div> <!-- #main-container -->

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>

        <script src="js/tinymce/tinymce.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <script>
            var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
    </body>
</html>
