<?php

require dirname(__FILE__) . '/class.image.php';

Class ImageResize extends Image {

    private $width;
    private $height;
    private $image_resized;
    private $image_aplha;

    function __construct($file, $tmp_file = NULL) {
        parent::open($file, $tmp_file);
        $this->width = parent::getWidth();
        $this->height = parent::getHeight();
        $this->image_aplha = parent::hasAplha();
    }

    public function resize($new_width, $new_height, $option = "auto") {
        // Get optimal width and height ( based on option )
        $option_array = $this->getDimensions($new_width, $new_height, $option);

        $optimal_width = $option_array['optimal_width'];
        $optimal_height = $option_array['optimal_height'];

        // Resample - Create image canvas of w, h size
        $this->image_resized = imagecreatetruecolor($optimal_width, $optimal_height);

        if ($this->image_aplha) {
            imagealphablending($this->image_resized, false);
            imagesavealpha($this->image_resized, true);
        }

        imagecopyresampled($this->image_resized, $this->image, 0, 0, 0, 0, $optimal_width, $optimal_height, $this->width, $this->height);

        // If CROP
        if ($option == 'crop') {
            $this->crop($optimal_width, $optimal_height, $new_width, $new_height);
        }

        $this->image = $this->image_resized;

        return $this->image;
    }

    private function getDimensions($new_width, $new_height, $option) {
        switch ($option) {
            case 'exact':
                $optimal_width = $new_width;
                $optimal_height = $new_height;
                break;
            case 'portrait':
                $optimal_width = $this->getSizeByFixedHeight($new_height);
                $optimal_height = $new_height;
                break;
            case 'landscape':
                $optimal_width = $new_width;
                $optimal_height = $this->getSizeByFixedWidth($new_width);
                break;
            case 'auto':
                $option_array = $this->getSizeByAuto($new_width, $new_height);
                $optimal_width = $option_array['optimal_width'];
                $optimal_height = $option_array['optimal_height'];
                break;
            case 'crop':
                $option_array = $this->getOptimalCrop($new_width, $new_height);
                $optimal_width = $option_array['optimal_width'];
                $optimal_height = $option_array['optimal_height'];
                break;
            default:
                echo 'Option "' . $option . '" not defined ';
                exit;
        }
        return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
    }

    private function getSizeByFixedHeight($new_height) {
        $ratio = $this->width / $this->height;
        $new_width = $new_height * $ratio;
        return $new_width;
    }

    private function getSizeByFixedWidth($new_width) {
        $ratio = $this->height / $this->width;
        $new_height = $new_width * $ratio;
        return $new_height;
    }

    private function getSizeByAuto($new_width, $new_height) {
        if ($this->height < $this->width) {
            //Image to be resized is wider (landscape)
            $optimal_width = $new_width;
            $optimal_height = $this->getSizeByFixedWidth($new_width);
        } elseif ($this->height > $this->width) {
            //Image to be resized is taller (portrait)
            $optimal_width = $this->getSizeByFixedHeight($new_height);
            $optimal_height = $new_height;
        } else {
            //Image to be resizerd is a square
            if ($new_height < $new_width) {
                $optimal_width = $new_width;
                $optimal_height = $this->getSizeByFixedWidth($new_width);
            } else if ($new_height > $new_width) {
                $optimal_width = $this->getSizeByFixedHeight($new_height);
                $optimal_height = $new_height;
            } else {
                //Sqaure being resized to a square
                $optimal_width = $new_width;
                $optimal_height = $new_height;
            }
        }

        return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
    }

    private function getOptimalCrop($new_width, $new_height) {

        $height_ratio = $this->height / $new_height;
        $width_ratio = $this->width / $new_width;

        if ($height_ratio < $width_ratio) {
            $optimal_ratio = $height_ratio;
        } else {
            $optimal_ratio = $width_ratio;
        }

        $optimal_height = $this->height / $optimal_ratio;
        $optimal_width = $this->width / $optimal_ratio;

        return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
    }

    private function crop($optimal_width, $optimal_height, $new_width, $new_height) {
        //Find center - this will be used for the crop
        $crop_start_x = ( $optimal_width / 2) - ( $new_width / 2 );
        $crop_start_y = ( $optimal_height / 2) - ( $new_height / 2 );

        $crop = $this->image_resized;

        //Now crop from center to exact requested size
        $this->image_resized = imagecreatetruecolor($new_width, $new_height);

        if ($this->image_aplha) {
            imagealphablending($this->image_resized, false);
            imagesavealpha($this->image_resized, true);
        }

        imagecopyresampled($this->image_resized, $crop, 0, 0, $crop_start_x, $crop_start_y, $new_width, $new_height, $new_width, $new_height);
    }

}
