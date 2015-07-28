<?php
// Include the class
include "../class.image.resize.php";

// Get image file
$image = $_FILES['imgFile']['name'];
$uploadedfile = $_FILES['imgFile']['tmp_name'];

// Configuration
// Format in which image should be saved
$resize_extension = "jpg";

// Image flename to save
$resize_filename = rand(1000000, 9999999);

// Path where to save image, leave blank if null
$resize_path = "../resized/";

// Image output size
$resize_width = 250;
$resize_height = 250;

// Image output option (exact, portrait, landscape, auto, crop)
$resize_option = "crop";

// Can be from 1 - 100 (lowest to highest)
$resize_quality = 100;

// Finally, resize image
// Initialise resize class
$resize = new ImageResize($image, $uploadedfile);

// Resize image
$resize->resizeImage($resize_width, $resize_height, $resize_option);

// Save image
$resize->saveImage($resize_path . $resize_filename . "." . $resize_extension, $resize_quality);

// All Done
$image_resized = $resize_path . $resize_filename . "." . $resize_extension;
?>

<!-- Output resized image -->
<img src="<?= $image_resized ?>" alt="" />