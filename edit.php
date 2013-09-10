<?php
    ///////////////
    // PHP stuff //
    ///////////////
    
    if (!extension_loaded('json')) {
            dl('json.so');  
    }

    $id = (int)$_GET["id"];

    include("../db_campaign.php");

    $where = "WHERE id LIKE \"$id\"";
    $limit = " LIMIT 0,1";

    $query = "SELECT * FROM `features`$where$limit";
    $result = mysql_query($query) or die("Fail: " . $query);   

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="css/normalize.min.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/story.css">

        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
        <script type="text/javascript" src="//use.typekit.net/hzy7lrd.js"></script>
        <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <div class="header-container">
            <header class="wrapper clearfix">
                <h1 class="title">Campaign Story Editor</h1>
               
            </header>
        </div>

        <div class="main-container">
            <div class="main wrapper clearfix">

                <article>
                 
                    <section>
                        <form>
                        <? 
                            while ($line = mysql_fetch_array($result)) {
                                $id = $line["id"];
                                $title = $line["title"];
                                $html = $line["html"];
                                $story_images = $line["story_images"];
                                $image_array = $array = json_decode($story_images,true);


                        ?>
                            <h2 class="storytitle"><?= $title?> (<a href="#" class="edit" data-edit="storytitle">Edit</a>)</h2>
                            <input class="storytitle" type="text" value="<?= $title?>" name="title"><input data-type="title" data-id="<?= $id?>" class="storytitle" type="submit" value="Submit" >

                            <div class="storyhtml">
                                <div class="editHtml">
                                    (<a data-edit="storyhtml" class="edit" href="#">Edit</a>)
                                </div>
                                <?= $html ?>

                            </div>
                            <div class="" id="textarea">
                                <textarea class="storyhtml"><?= $html ?></textarea>
                            </div>
                            <input data-lookfor="textarea" data-type="html" data-id="<?= $id?>" class="storyhtml" type="submit" value="Submit" >
                            
                            <input type="file">
                            <?php 
                            foreach($image_array as $k => $v) {
                                echo "<img class='fromdb' src='".$v["url"]."' alt='image'/>";

                            }
                            ?>


                       <?
                            }
                       ?>
                        </form>
                    </section>
         
                </article>

            </div> <!-- #main -->
        </div> <!-- #main-container -->

        <div class="footer-container">
            <footer class="wrapper">
                <h3>footer</h3>
            </footer>
        </div>

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
