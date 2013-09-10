<? 

	include('../db_campaign.php');

	$id = (int)$_GET['id'];
	$query = "SELECT * FROM `messages` WHERE `id` LIKE $id LIMIT 0,1";
	$result = mysql_query($query) or die("Sorry.");
	$line = mysql_fetch_array($result);

	$body = $line["message"];

	$hasimages = false;

	$images = array(
		1 => $line["img1"], 
		2 => $line["img2"],
		3 => $line["img3"],
		4 => $line["img4"],
		5 => $line["img5"],
		6 => $line["img6"],
		7 => $line["img7"],
		8 => $line["img8"],
		9 => $line["img9"],
		10 => $line["img10"]
	);

	$imgurls = array();
	foreach($images as $k => $v) {
		if ($v != null) {
			$hasimages = true;
			$imgurls[] = $v;
		}
	}

 ?>

<html>
<head>
	<title>Message preview</title>
	<link rel="stylesheet" href="css/preview.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<script type="text/javascript" src="//use.typekit.net/hzy7lrd.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	<!-- SlidesJS Required (if responsive): Sets the page width to the device width. -->
	<meta name="viewport" content="width=device-width">
	<!-- End SlidesJS Required -->

	<!-- SlidesJS Required: These styles are required if you'd like a responsive slideshow -->
	<style>
	/* Prevent the slideshow from flashing on load */
	#slides {
      display: none
    }

    #slides .slidesjs-navigation {
      margin-top:3px;
    }

    #slides .slidesjs-previous {
      margin-right: 5px;
      float: left;
    }

    #slides .slidesjs-next {
      margin-right: 5px;
      float: left;
    }

    .slidesjs-pagination {
      margin: 6px 0 0;
      float: right;
      list-style: none;
    }

    .slidesjs-pagination li {
      float: left;
      margin: 0 1px;
    }

    .slidesjs-pagination li a {
      display: block;
      width: 13px;
      height: 0;
      padding-top: 13px;
      background-image: url(img/pagination.png);
      background-position: 0 0;
      float: left;
      overflow: hidden;
    }

    .slidesjs-pagination li a.active,
    .slidesjs-pagination li a:hover.active {
      background-position: 0 -13px
    }

    .slidesjs-pagination li a:hover {
      background-position: 0 -26px
    }

    #slides a:link,
    #slides a:visited {
      color: #333
    }

    #slides a:hover,
    #slides a:active {
      color: #9e2020
    }

    .navbar {
      overflow: hidden
    }
  </style>
  <!-- End SlidesJS Optional-->

  <!-- SlidesJS Required: These styles are required if you'd like a responsive slideshow -->
  <style>
    #slides {
      display: none
    }

    .container {
      margin: 0 auto
    }

    /* For tablets & smart phones */
    @media (max-width: 767px) {
      body {
        padding-left: 20px;
        padding-right: 20px;
      }
      .container {
        width: auto
      }
    }

    /* For smartphones */
    @media (max-width: 480px) {
      .container {
        width: auto
      }
    }

    /* For smaller displays like laptops */
    @media (min-width: 768px) and (max-width: 979px) {
      .container {
        width: 724px
      }
    }

    /* For larger displays */
    @media (min-width: 1200px) {
      .container {
        width: 1170px
      }
    }
	</style>
</head>
<body>

<header class="sticky" id="nav">
    <ul>
        <li class="home"><a href="http://engin.umich.edu"><img src="img/mighigan_engineering_25.png" alt="Michigan Engineering"></a></li>
    </ul>
    <div id="go-back">
        <span>Home</span>
        <div class="square">
            <p class="one"></p>
            <p class="two"></p>
            <p class="three"></p>
            <p class="four"></p>
        </div>
    </div>
    <div id="switch" style="display: none;">
        <span>Explore</span>
        <div class="square">
            <p class="one"></p>
            <p class="two"></p>
            <p class="three"></p>
            <p class="four"></p>
        </div>
    </div>
</header>

<div class="item-content">
	<div class="content-image-div">
		<img class="content-image" src="img/big/mail.jpg" alt="item image test">
	</div>
	<div class="content-info" style="margin-top: 210px;">
		<h2 class="fadewithme">Read your messages</h2>
		<h3>Message Preview</h3>
		<div class="body"><?= $body?></div>
		<div class="container">
	    <? if ($hasimages) { ?>

	    <div id="slides">

	    	<? foreach($imgurls as $k => $v) { ?>
				<img src="http://localhost:8888/htdocs/DME/campaign/img/uploads/<?= $v?>" alt="Photo <? $kk = $k+1; echo $kk?>">

			<? } ?>


			<a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"></i></a>
			<a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"></i></a>
	    </div>

	    <? } ?>
	  </div>
	</div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="js/jquery.slides.min.js"></script>
<script>
    $(function() {
      $('#slides').slidesjs({
        width: 940,
        height: 528,
        navigation: false
      });
    });
  </script>
</body>
</html>