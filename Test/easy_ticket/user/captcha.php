<?php
ob_start();
session_start();

$width  = 150; 
$height =  30; 

$im = @imagecreatetruecolor(150, 50) or die('Cannot Initialize new GD image stream');
$text_color = imagecolorallocate($im, 0, 0, 0);

// set background color and fill
$background = imagecolorallocate($im, 255, 255, 250);
imagefill($im, 0, 0, $background);

// randomly generate code size
$str_size = mt_rand(5,10);

// characters to use in code
$str = "abcdefghijklmnopqrstuvwxyz0123456789";

// split characters into an array
$str_array = str_split($str);

// generate random keys from the character array up to the random code size
$rand_keys = array_rand($str_array, $str_size);

// draw lines across image
for( $i=0; $i<10; $i++ ) { 
   imageline($im,  
         mt_rand(0,$width), mt_rand(0,$height),  
         mt_rand(0,$width), mt_rand(0,$height),  
         imagecolorallocate($im, mt_rand(150,255),  
                                    mt_rand(150,255),  
                                    mt_rand(150,255))); 
} 

// loop through character array and random keys until size is reached.
for ($i = 1; $i <= $str_size; $i++) {

	$char = $str_array[$rand_keys[$i]];
	imagechar($im, 5, $i*15, mt_rand(10,20), $char, $text_color);
    $code .= $char;

}


header ('Content-Type: image/png');
imagepng($im);
imagedestroy($im);

$_SESSION['securityCode'] = $code;

ob_end_flush();
?>