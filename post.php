<?php

/**
 * Include the class
 */
include("include/resize-class.php");

/**
 * Get image file
 */
$image = $_FILES['myFile']['name'];
$uploadedfile = $_FILES['myFile']['tmp_name'];

/**
 * Configuration
 */
//Save the image as ?
$export_extension = "jpg";
//Image flename to save
$export_filename = rand(1000000, 9999999);
//Path where to save image, leave blank if null
$export_path = "resized/";
//Output width
$export_width = 250;
//Output height
$export_height = 250;
//Output option (exact, portrait, landscape, auto, crop)
$export_option = "crop";
//Can be from 1 - 100
$export_quality = 100;

/**
 * Finally, resize image
 */
//Initialise / load image
$resizeObj = new resize($image, $uploadedfile);

//Resize image
$resizeObj->resizeImage($export_width, $export_height, $export_option);

//Save image
$resizeObj->saveImage($export_path . $export_filename . "." . $export_extension, $export_quality);

//All Done, echo the save path
echo "Image saved to : " . $export_path . $export_filename . "." . $export_extension;
?>
