<?php

namespace App\Helpers;


use DB;
use Carbon\Carbon;

class Common {

     public static function generateThumbnails($source_image_path, $thumbnail_image_path, $reduceSize, $reduceSizePercentage, $thumbnailMaxWidth, $thumbnailMaxHeight, $maintainAspectRatio, $cropImage , $bgColor = 0, $quality = 100) {

        //The getimagesize() function will determine the size of any supported given image file
        //and return the dimensions along with the file type and a height/width
        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);

        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $source_gd_image = imagecreatefromgif($source_image_path);
                break;
            case IMAGETYPE_JPEG:
                // imagecreatefromjpeg() returns an image identifier representing the image obtained from the given filename
                $source_gd_image = imagecreatefromjpeg($source_image_path);
                break;
            case IMAGETYPE_PNG:
                $source_gd_image = imagecreatefrompng($source_image_path);
                break;
        }

        if ($source_gd_image === false) {
            // Return false if image type is unknown
            return false;
        }

        // First check reduce size parameter is true or false        
        if ($reduceSize == true) {
            $reducedWidth = $source_image_width * $reduceSizePercentage;
            $reducedHeight = $source_image_height * $reduceSizePercentage;

            // Create a new true color image
            // imagecreatetruecolor() returns an image identifier representing a black image of the specified size
            $thumbnail_gd_image = imagecreatetruecolor($reducedWidth, $reducedHeight);

            // imagecopyresampled() copies a rectangular portion of one image to another image,
            // smoothly interpolating pixel values so that, in particular, reducing the size of an image still retains a great deal of clarity
            imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $reducedWidth, $reducedHeight, $source_image_width, $source_image_height);

            // imagejpeg() creates a JPEG file from the given image
            imagejpeg($thumbnail_gd_image, $thumbnail_image_path, $quality);
            imagedestroy($source_gd_image);
            imagedestroy($thumbnail_gd_image);
            return true;
        } else if ($maintainAspectRatio == true) { // Then check $maintainAspectRatio parameter is true or false        
            // Custome code ends here
            // Calculating aspect ratio of original image
            $source_aspect_ratio = $source_image_width / $source_image_height;

            // Calculating aspect ratio of thumbnail image
            $thumbnail_aspect_ratio = $thumbnailMaxWidth / $thumbnailMaxHeight;

            if ($source_image_width <= $thumbnailMaxWidth && $source_image_height <= $thumbnailMaxHeight) {
                // If original image is smaller than thumbnail image
                $thumbnail_image_width = $source_image_width;
                $thumbnail_image_height = $source_image_height;
            } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
                // thumbnail aspect ratio is greater than original image aspect ratio
                $thumbnail_image_width = (int) ($thumbnailMaxHeight * $source_aspect_ratio);
                $thumbnail_image_height = $thumbnailMaxHeight;
            } else {
                // thumbnail aspect ratio is smaller than original image aspect ratio
                $thumbnail_image_width = $thumbnailMaxWidth;
                $thumbnail_image_height = (int) ($thumbnailMaxWidth / $source_aspect_ratio);
            }

            // Create a new true color image
            // imagecreatetruecolor() returns an image identifier representing a black image of the specified size
            $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);

            // imagecopyresampled() copies a rectangular portion of one image to another image,
            // smoothly interpolating pixel values so that, in particular, reducing the size of an image still retains a great deal of clarity
            imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);

            // Create a new true color image
            $img_disp = imagecreatetruecolor($thumbnailMaxWidth, $thumbnailMaxWidth);
            // Allocate a bg color for an image
            $backcolor = imagecolorallocate($img_disp, $bgColor, $bgColor, $bgColor);
            // Performs a flood fill starting at the given coordinate (top left is 0, 0) with the given color in the image
            imagefill($img_disp, 0, 0, $backcolor);

            // Copy a part of src_im onto dst_im starting at the x,y coordinates src_x, src_y with a width of src_w and a height of src_h.
            // The portion defined will be copied onto the x,y coordinates, dst_x and dst_y.
            imagecopy($img_disp, $thumbnail_gd_image, (imagesx($img_disp) / 2) - (imagesx($thumbnail_gd_image) / 2), (imagesy($img_disp) / 2) - (imagesy($thumbnail_gd_image) / 2), 0, 0, imagesx($thumbnail_gd_image), imagesy($thumbnail_gd_image));

            // imagejpeg() creates a JPEG file from the given image
            imagejpeg($img_disp, $thumbnail_image_path, $quality);
            imagedestroy($source_gd_image);
            imagedestroy($thumbnail_gd_image);
            imagedestroy($img_disp);
            return true;
        } elseif ($cropImage == true) { // Then check $cropImage parameter is true or false
                //$image = imagecreatefromjpeg($source_image_path);
            $info = getimagesize($source_image_path);

        if ($info['mime'] == 'image/jpeg')
              $image = imagecreatefromjpeg($source_image_path);

        elseif ($info['mime'] == 'image/gif')
              $image = imagecreatefromgif($source_image_path);

      elseif ($info['mime'] == 'image/png')
              $image = imagecreatefrompng($source_image_path);
      
            $filename = $thumbnail_image_path;
            $thumb_width = $thumbnailMaxWidth;
            $thumb_height = $thumbnailMaxHeight;
            $width = imagesx($image);
            $height = imagesy($image);
            $original_aspect = $width / $height;
            $thumb_aspect = $thumb_width / $thumb_height;
            if ($original_aspect >= $thumb_aspect) {
                // If image is wider than thumbnail (in aspect ratio sense)
                $new_height = $thumb_height;
                $new_width = $width / ($height / $thumb_height);
            } else {
                // If the thumbnail is wider than the image
                $new_width = $thumb_width;
                $new_height = $height / ($width / $thumb_width);
            }
            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
            // Resize and crop
            imagecopyresampled(
                    $thumb, $image, 0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                    0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                    0, 0, $new_width, $new_height, $width, $height
            );
            imagejpeg($thumb, $filename, 100);
            imagedestroy($thumb);
            return true;
        } else {
            // Create a new true color image
            // imagecreatetruecolor() returns an image identifier representing a black image of the specified size
            $thumbnail_gd_image = imagecreatetruecolor($thumbnailMaxWidth, $thumbnailMaxHeight);

            // imagecopyresampled() copies a rectangular portion of one image to another image,
            // smoothly interpolating pixel values so that, in particular, reducing the size of an image still retains a great deal of clarity
            imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnailMaxWidth, $thumbnailMaxHeight, $source_image_width, $source_image_height);

            // imagejpeg() creates a JPEG file from the given image
            imagejpeg($thumbnail_gd_image, $thumbnail_image_path, $quality);
            imagedestroy($source_gd_image);
            imagedestroy($thumbnail_gd_image);
            return true;
        }
    }

    
}
