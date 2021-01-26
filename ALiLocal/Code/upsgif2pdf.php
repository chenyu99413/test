<?php
$source=tempnam(sys_get_temp_dir(), 'Tux').'.gif';
$target=tempnam(sys_get_temp_dir(), 'Tux').'.pdf';

$body=base64_decode(file_get_contents("php://input"));
if (empty($body)){
	exit('No data.');
}
file_put_contents($source, $body);
if (PHP_OS =='Darwin'){
	# macOS
	system("/usr/local/bin/magick convert \"{$source}\" -quality 100 -units PixelsPerInch -density 300x300 -resize 1800x1200 -rotate 90 \"{$target}\"",$ret);
}else {
	# centOS /usr/bin/convert
	system("/usr/bin/convert \"{$source}\" -quality 100 -units PixelsPerInch -density 300x300 -resize 1800x1200 -rotate 90 \"{$target}\"",$ret);
}
if ($ret !=0){
	exit('Convert fail.');
}
@header("Content-type:application/pdf");
readfile($target);