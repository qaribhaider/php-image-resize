<?php

Class Image {

    protected $image;
    private $alpha = false;

    public function open($file, $tmp_file = NULL) {
        $extension = $this->getExtention($file, $tmp_file);
        $file = ($tmp_file != NULL) ? $tmp_file : $file;

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($file);
                $this->alpha = false;
                break;
            case 'gif':
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($file);
                $this->alpha = false;
                break;
            case 'png':
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($file);
                imagealphablending($this->image, true);
                $this->alpha = true;
                break;
            default:
                $this->image = false;
                break;
        }

        return $this->image;
    }

    public function save($filename, $quality = "100") {
        $extension = $this->getExtention($filename, null, false);

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                if (imagetypes() & IMG_JPG) {
                    imagejpeg($this->image, $filename, $quality);
                }
                break;

            case 'gif':
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->image, $filename);
                }
                break;

            case 'png':
                if (imagetypes() & IMG_PNG) {
                    //Scale quality from 0-100 to 0-9
                    $scale_quality = round(($quality / 100) * 9);

                    //Invert quality setting as 0 is best, not 9
                    $invert_scale_quality = 9 - $scale_quality;

                    imagepng($this->image, $filename, $invert_scale_quality);
                }
                break;

            default:
                // No extension - No save.
                break;
        }

        imagedestroy($this->image);
    }

    private function getExtention($file, $tmp_file, $check_mime = true) {
        if ($check_mime) {
            $ext = exif_imagetype($tmp_file);
        }

        if (!$check_mime || !$ext) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
        }

        return strtolower($ext);
    }

    public function getWidth() {
        return (int) imagesx($this->image);
    }

    public function getHeight() {
        return (int) imagesy($this->image);
    }

    public function hasAplha() {
        return (boolean) $this->alpha;
    }

}
