<?php
//error_reporting(E_ALL);
//sae_xhprof_start();
//flush();

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

function loadRawData($rawdata)
{
    $im = @imagecreatefromstring($rawdata); /* Attempt to open */
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

function loadFileRange($url)
{
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);

    curl_setopt($ch, CURLOPT_RANGE,"1-100000");

    $data = curl_exec($ch);

    return $data;

}

function retrieve_remote_file_size($url){
     $ch = curl_init($url);

     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
     curl_setopt($ch, CURLOPT_HEADER, TRUE);
     curl_setopt($ch, CURLOPT_NOBODY, TRUE);

     $data = curl_exec($ch);
     $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

     curl_close($ch);
     return $size;
}


//$image_to_output = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");

//$images[0] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");
//$images[1] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");
//$images[2] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");
//$images[3] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");
//$images[4] = LoadJpeg("http://ww2.sinaimg.cn/thumbnail/6d39fb37jw1dm8vqmssdxj.jpg");

$nHeight = $_GET["height"];
$nWidth = $_GET["width"];
$nNum = $_GET["num"];

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}



//dowload images
for ($i = 0; $i < $nNum; $i++) {
    $url = $_GET["img"][$i];

    $size = retrieve_remote_file_size($url);

    $flag_too_large[$i] = 0;

    
    if ($size > 100000) {
        //to bmiddle pic.
        $url = str_replace("large", "bmiddle", $url);
        $size = retrieve_remote_file_size($url);
        if ($size > 100000) {
            //still too large.
            $flag_too_large[$i] = 1;
            $raw = loadFileRange($url);
            $images[i] = loadRawData($raw);
        }
    }

    $images[$i] = imagecreatefromstring(file_get_contents($url));

}


$ret_image = imagecreatetruecolor((int)$nWidth * $nNum, (int)$nHeight);


//resample and merge images
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

    if ($flag_too_large[$i] == 1) {
        $src_x = 0;
        $src_y = 0;
    }
    
    $ret = imagecopyresampled($ret_image, $images[$i], $nWidth * $i, 0, $src_x, $src_y, $nWidth, $nHeight, $src_w, $src_h);
}


header('Content-Type: image/jpeg');
imagejpeg($ret_image);

//sae_xhprof_end();

?>
