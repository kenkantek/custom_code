<?php
class FileHelper
{
    public static function unique_string($length=32, $pool="")
	{
		// set pool of possible char
		if($pool == "")
		{
			$pool = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$pool .= "abcdefghijklmnopqrstuvwxyz";
			$pool .= "0123456789";
		}

		mt_srand ((double) microtime() * 1000000);
		$unique_id = "";
		for ($index = 0; $index < $length; $index++)
		{
			$unique_id .= substr($pool, (mt_rand()%(strlen($pool))), 1);
		}

		return strtoupper(sha1($unique_id));
	}

	public static function checkFile($fileObj, $path, $checksize = array(), &$error = '' ,$file_name = null)
    {
        $mime_type	= $fileObj['type'];
        $temp_path	= $fileObj['tmp_name'];
		$size		= $fileObj['size'];

		//check MIME type, allow JPG or PNG
		$mimeType = array('image/jpeg'      => 'jpg',
							'image/pjpeg'   => 'jpg',
							'image/png'     => 'png',
							'image/gif'     => 'gif',
		);

		if (!isset($mimeType[$mime_type]))
		{
			$error = 'Wrong image type. Format allowed are '.  implode(', ',array_values($mimeType));
			return false;
		}

        //not file uploaded via HTTP_POST
        if (!is_uploaded_file($temp_path))
		{
			$error = 'Wrong method of uploading file';
			return false;
		}

		//check size, in MB
		if($size >= (1024 * 1024 * IMAGE_MAX_SIZE))
		{
			$error = 'Image size <= '. IMAGE_MAX_SIZE . 'M';
			return false;
		}

		//check folder exsited
		if(!@is_dir($path))
		{
			if(@mkdir($path) !== TRUE)
			{
				$error = "Folder $path is not exsited and can not created by coding. Please create it by manual";
				return false;
			}
		}

		$ext = $mimeType[$mime_type];

		//check size, param must be w,h
		if(!empty($checksize))
		{
			$w = $checksize[0];
			$h = $checksize[1];
			if(!FileHelper::checkImageSize($temp_path, $ext, $w, $h))
			{
				$error = 'Wrong image size. Width <= '.$w.' and Height <= '.$h;
				return false;
			}
		}


		//if no given filename, generate it
		if (is_null($file_name))
			$new_file_name = FileHelper::unique_string().".".$ext;
		else
			$new_file_name = $file_name;

		$new_path = $path.$new_file_name;

		//if(move_uploaded_file($temp_path, $new_path)) return $new_file_name;

			$pathToImage = $temp_path;

			//resize
			if($ext == 'jpg' || $ext == 'jpeg')
			{
				$img = @imagecreatefromjpeg( "{$pathToImage}" );
			}
			elseif($ext == 'png')
			{
				$img = @imagecreatefrompng( "{$pathToImage}" );
			}
			elseif($ext == 'gif')
			{
				$img = @imagecreatefromgif( "{$pathToImage}" );
			}

			if(!$img)
			{
				$error = 'Cannot create image object from '.$pathToImage.'. Possible missing GD library or wrong file path';
				return false;
			}

			$width  = imagesx( $img );
			$height = imagesy( $img );

			// calculate thumbnail size
			$new_width	= IMAGE_WIDTH;
			$new_height = IMAGE_HEIGHT;
//			$new_height = floor( $height * ( $new_width / $width ) );
//
//			if ($new_height > IMAGE_HEIGHT)
//			{
//				$new_height = IMAGE_HEIGHT;
//				$new_width = floor( $width * ( $new_height / $height ) );
//			}

			// create a new temporary image
			$tmp_img = imagecreatetruecolor( $new_width, $new_height );

			// copy and resize old image into new image
			imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

			$pathToThumb = $new_path;

			// save thumbnail into a file
			if ($ext == 'png')
			{
				if(!@imagepng($tmp_img, "{$pathToThumb}"))
				{
					$error = 'Can not create file at '.$pathToThumb.'. Please try again';
					return false;
				}
			}
			elseif($ext == 'jpg')
			{
				if(!@imagejpeg( $tmp_img, "{$pathToThumb}"))
				{
					$error = 'Can not create file at '.$pathToThumb.'. Please try again';
					return false;
				}
			}
			elseif($ext == 'gif')
			{
				if(!@imagegif( $tmp_img, "{$pathToThumb}"))
				{
					$error = 'Can not create file at '.$pathToThumb.'. Please try again';
					return false;
				}
			}

			return $new_file_name;


		$error = 'Can not upload file. Please try again';
		return false;
    }

	public static function checkImageSize($filename, $ext, $w, $h)
	{
		if($ext == 'jpg' || $ext == 'jpeg')
		{
			$img = imagecreatefromjpeg( "{$filename}" );
		}
		elseif($ext == 'png')
		{
			$img = imagecreatefrompng( "{$filename}" );
		}
		elseif($ext == 'gif')
		{
			$img = imagecreatefromgif($filename);
		}
		else
		{
			return false;
		}

		$width = imagesx($img);
		$height = imagesy($img);

		if($width > $w || $height > $h)
			return false;

		return true;
	}

	function generateFileName($fileName,$ext)
	{
		$temp = explode(".",$fileName);
		array_pop($temp);

		return implode("", $temp) . '_'. time() . '.' . $ext;
	}

	function createThumbs( $pathToImage,$thumbFolder, $W, $H)
	{
		try
		{
			$info		= pathinfo($pathToImage);
			$ext		= strtolower($info['extension']);
			$filename	= $info['filename'];

			if($ext == 'jpg' || $ext == 'jpeg')
			{
				$img = imagecreatefromjpeg( "{$pathToImage}" );
			}
			elseif($ext == 'png')
			{
				$img = imagecreatefrompng( "{$pathToImage}" );
			}
			elseif($ext == 'gif')
			{
				$img = imagecreatefromgif( "{$pathToImage}" );
			}

			if(!$img) return false;

			$width  = imagesx( $img );
			$height = imagesy( $img );

			// calculate thumbnail size
			$new_width = $W;
			$new_height = floor( $height * ( $new_width / $width ) );

			if ($new_height > $H)
			{
				$new_height = $H;
				$new_width = floor( $width * ( $new_height / $height ) );
			}

			// create a new temporary image
			$tmp_img = imagecreatetruecolor( $new_width, $new_height );

			// copy and resize old image into new image
			imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

			//generate new file name
			$new_file_name = $filename . '.' . $ext;
			$pathToThumb = $thumbFolder . $new_file_name;

			// save thumbnail into a file
			if ($ext == 'png')
			{
				if(!imagepng($tmp_img, "{$pathToThumb}")) return false;
			}
			elseif($ext == 'jpg')
			{
				if(!imagejpeg( $tmp_img, "{$pathToThumb}")) return false;
			}
			elseif($ext == 'gif')
			{
				if(!imagegif( $tmp_img, "{$pathToThumb}")) return false;
			}

			return $new_file_name;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public static function checkFileGallery($fileObj, $path, $checksize = array(), &$error = '' ,$file_name = null)
    {
        $mime_type	= $fileObj['type'];
        $temp_path	= $fileObj['tmp_name'];
		$size		= $fileObj['size'];

		//check MIME type, allow JPG or PNG
		$mimeType = array('image/jpeg'      => 'jpg',
							'image/pjpeg'   => 'jpg',
							'image/png'     => 'png',
							'image/gif'     => 'gif',
		);

		if (!isset($mimeType[$mime_type]))
		{
			$error = 'Wrong image type. Format allowed are '.  implode(', ',array_values($mimeType));
			return false;
		}

        //not file uploaded via HTTP_POST
        if (!is_uploaded_file($temp_path))
		{
			$error = 'Wrong method of uploading file';
			return false;
		}

//		//check size, in MB
//		if($size >= (1024 * 1024 * IMAGE_MAX_SIZE))
//		{
//			$error = 'Image size <= '. IMAGE_MAX_SIZE . 'M';
//			return false;
//		}

		//check folder exsited
		if(!@is_dir($path))
		{
			if(@mkdir($path) !== TRUE)
			{
				$error = "Folder $path is not exsited and can not created by coding. Please create it by manual";
				return false;
			}
		}

		$ext = $mimeType[$mime_type];

		//check size, param must be w,h
		if(!empty($checksize))
		{
			$w = $checksize[0];
			$h = $checksize[1];
			if(!FileHelper::checkImageSize($temp_path, $ext, $w, $h))
			{
				$error = 'Wrong image size. Width <= '.$w.' and Height <= '.$h;
				return false;
			}
		}


			//if no given filename, generate it
			if (is_null($file_name))
				$new_file_name = FileHelper::unique_string().".".$ext;
			else
				$new_file_name = $file_name;

			$pathToImage = $temp_path;

			//resize
			if($ext == 'jpg' || $ext == 'jpeg')
			{
				$img = @imagecreatefromjpeg( "{$pathToImage}" );
			}
			elseif($ext == 'png')
			{
				$img = @imagecreatefrompng( "{$pathToImage}" );
			}
			elseif($ext == 'gif')
			{
				$img = @imagecreatefromgif( "{$pathToImage}" );
			}

			if(!$img)
			{
				$error = 'Cannot create image object from '.$pathToImage.'. Possible missing GD library or wrong file path';
				return false;
			}

			$width  = imagesx( $img );
			$height = imagesy( $img );

			// calculate thumbnail size
			$new_width	= GALLERY_THUMB_IMAGE_WIDTH;
			$new_height = GALLERY_THUMB_IMAGE_HEIGHT;

			// create a new temporary image
			$tmp_img = imagecreatetruecolor( $new_width, $new_height );

			// copy and resize old image into new image
			imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );


			//create thumb

			$pathToThumb = $path.'thumb_'.$new_file_name;

			// save thumbnail into a file
			if ($ext == 'png')
			{
				if(!@imagepng($tmp_img, "{$pathToThumb}"))
				{
					$error = 'Can not create file at '.$pathToThumb.'. Please try again';
					return false;
				}
			}
			elseif($ext == 'jpg')
			{
				if(!@imagejpeg( $tmp_img, "{$pathToThumb}"))
				{
					$error = 'Can not create file at '.$pathToThumb.'. Please try again';
					return false;
				}
			}
			elseif($ext == 'gif')
			{
				if(!@imagegif( $tmp_img, "{$pathToThumb}"))
				{
					$error = 'Can not create file at '.$pathToThumb.'. Please try again';
					return false;
				}
			}

			//for origin file
			$new_path = $path.$new_file_name;

			//auto resize
			$new_width	= GALLERY_IMAGE_WIDTH;
			$new_height = GALLERY_IMAGE_HEIGHT;

			// create a new temporary image
			$tmp_img = imagecreatetruecolor( $new_width, $new_height );

			// copy and resize old image into new image
			imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

			$pathToBig = $path.$new_file_name;

			// save thumbnail into a file
			if ($ext == 'png')
			{
				if(!@imagepng($tmp_img, "{$pathToBig}"))
				{
					@unlink($pathToThumb);
					$error = 'Can not create file at '.$pathToBig.'. Please try again';
					return false;
				}
			}
			elseif($ext == 'jpg')
			{
				if(!@imagejpeg( $tmp_img, "{$pathToBig}"))
				{
					@unlink($pathToThumb);
					$error = 'Can not create file at '.$pathToBig.'. Please try again';
					return false;
				}
			}
			elseif($ext == 'gif')
			{
				if(!@imagegif( $tmp_img, "{$pathToBig}"))
				{
					@unlink($pathToThumb);
					$error = 'Can not create file at '.$pathToBig.'. Please try again';
					return false;
				}
			}


			return $new_file_name;


		$error = 'Can not upload file. Please try again';
		return false;
    }
}