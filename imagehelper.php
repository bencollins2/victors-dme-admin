<?php

if (!extension_loaded('json')) {
        dl('gd.so');  
}
function LoadJpeg($imgname)
{
    /* Attempt to open */
    $im = @imagecreatefromjpeg($imgname);
    /* See if it failed */
    if(!$im)
    {
        /* Create a black image */
        $im  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);

        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

        /* Output an error message */
        imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}

header('Content-Type: image/jpeg');

$imgname = $_REQUEST["i"];
$img = LoadJpeg('/afs/umich.edu/group/e/engcomm/Private/uploads/img/'.$imgname.'.jpg');

imagejpeg($img);
imagedestroy($img);
?>