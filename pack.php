<?php

function LoadJpeg($imgname)
{
    $im = @imagecreatefromjpeg($imgname); /* Attempt to open */
    if(!$im) { /* See if it failed */
        $im  = imagecreatetruecolor(150, 30); /* Create a blank image */
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        /* Output an errmsg */
        imagestring($im, 1, 5, 5, "Error loading $imgname", $tc);
    }
    return $im;
}

function LoadGif($imgname)
{
    $im = @imagecreatefromgif($imgname); /* Attempt to open */
    if(!$im) { /* See if it failed */
        $im  = imagecreatetruecolor(150, 30); /* Create a blank image */
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        /* Output an errmsg */
        imagestring($im, 1, 5, 5, "Error loading $imgname", $tc);
    }
    return $im;

}

//$image_to_output = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");

//$images[0] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");
//$images[1] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");
//$images[2] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");
//$images[3] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");
//$images[4] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");

$nHeight = $_POST["height"];
$nWidth = $_POST["width"];
$nNum = $_POST["num"];

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}


for ($i = 0; $i < $nNum; $i++) {
    $url = $_POST["img"][$i];
    if (endsWith($url, ".jpg")) {
        $images[$i] = LoadJpeg($url);
    }
    if (endsWith($url, ".gif")) {
        $images[$i] = LoadGif($url);
    }
}


$ret_image = imagecreatetruecolor((int)$nWidth * $nNum, (int)$nHeight);



for ($i = 0; $i < $nNum; $i++ ) {
    $oHeight = imagesy($images[$i]);
    $oWidth = imagesx($images[$i]);
    
    $xyRate = $nWidth * 100 / $nHeight;
    
    if ($oHeight * $xyRate / 100 < $oWidth) {
    	$src_h = $oHeight;
    	$src_w = $oHeight * $xyRate / 100;
    	$src_y = 0;
    	$src_x = ($oWidth - $src_w) / 2;
    }
    else {
        $src_w = $oWidth;
        $src_h = $oWidth * 100 / $xyRate;
        $src_x = 0;
        $src_y = ($oHeight - $src_h) / 2;
    }
    
    $ret = imagecopyresampled($ret_image, $images[$i], $nWidth * $i, 0, $src_x, $src_y, $nWidth, $nHeight, $src_w, $src_h);
}



header('Content-Type: image/jpeg');
imagejpeg($ret_image);

?>

