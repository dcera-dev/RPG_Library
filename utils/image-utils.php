<?php
    namespace Utilities;

use Exception;

    class ImageUtils {
        private static function resizeImage($origin, $type, $maxPixels) {
            $img = '';
            if ($type === IMAGETYPE_PNG) {
                $img = imagecreatefrompng($origin);
            }
            else if ($type === IMAGETYPE_JPEG) {
                $img = imagecreatefromjpeg($origin);
            }
            else {
                return 0; //Unsupported
            }
            //Get Image Width & Height
            $imgW = imagesx($img);
            $imgH = imagesy($img);

            //Calc Ratios
            $rWidth = $imgW / $maxPixels;
            $rHeight = $imgH / $maxPixels;
            $ratio = max($rWidth, $rHeight);

            //Calc New Height & Width
            $nWidth = round($imgW / $ratio);
            $nHeight = round($imgH / $ratio);

            //Create New Blank Image
            $resImg = imagecreatetruecolor($nWidth, $nHeight);

            //Copy Image Data to New
            imagecopyresampled($resImg, $img, 0, 0, 0, 0, $nWidth, $nHeight, $imgW, $imgH);

            //Clear Image Data
            imagedestroy($img);

            //Return Resized Image
            return $resImg;
        }
        public static function processImage($file, $path) {
            //$file = image file
            //$path = path to save resized file
            $imgType = getimagesize($file)[2];
            $imgResize = self::resizeImage($file, $imgType, 600);

            if ($imgResize == 0) {
                //Unsupported filetype
                unlink($file);
                return '../db_storage/unsupported.png';
            }

            $outputPath = '';

            //Create Files
            switch ($imgType) {
                case IMAGETYPE_PNG :
                    $outputPath = $path . '/profile.png';
                    imagepng($imgResize, $outputPath);
                    break;
                case IMAGETYPE_JPEG :
                    $outputPath = $path . '/profile.jpg';
                    imagejpeg($imgResize, $outputPath);
                    break;
                default: exit;
            }

            imagedestroy($imgResize);
            unlink($file);
            return $outputPath;
        }
    }