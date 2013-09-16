<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function resize_image($image, $sourcefolder, $destinationfolder, $width, $height)
{
    $CI =& get_instance();

    $data['image'] = $sourcefolder."/".$image['file_name'];


    $config['image_library'] = 'gd2';
    $config['source_image'] = $data['image'];
    //$config['create_thumb'] = TRUE;
    $config['maintain_ratio'] = TRUE;
    $config['width'] = $width;
    $config['height'] = $height;
    $config['new_image'] = $destinationfolder."/".$image['file_name'];

    $CI->load->library('image_lib');
    $CI->image_lib->initialize($config);
    $CI->image_lib->resize();
}

function convert_to_base_64($raw_data, $width, $height)
{
    ob_start();
    $data = explode(",", $raw_data);

    ob_clean();

    $image = imagecreatetruecolor($width, $height);
    $background = imagecolorallocate($image,0, 0, 0);

    $i = 0;
    for($x=0; $x<=$width; $x++)
    {
	for($y=0; $y<=$height; $y++)
	{
	    $int = hexdec($data[$i++]);
	    $color = ImageColorAllocate ($image, 0xFF & ($int >> 0x10), 0xFF & ($int >> 0x8), 0xFF & $int);
	    imagesetpixel ($image, $x, $y, $color);
	}
    }

    //Output image and clean
    imageJPEG($image);
    imagedestroy($image);
    $chart = ob_get_contents();
    ob_end_clean();

    return chunk_split(base64_encode($chart));
}

function convert_to_image($raw_data, $width, $height)
{
    ob_start();
    $data = explode(",", $raw_data);

    ob_clean();

    $image = imagecreatetruecolor($width, $height);
    $background = imagecolorallocate($image,0, 0, 0);

    $i = 0;
    for($x=0; $x<=$width; $x++)
    {
	for($y=0; $y<=$height; $y++)
	{
	    $int = hexdec($data[$i++]);
	    $color = ImageColorAllocate ($image, 0xFF & ($int >> 0x10), 0xFF & ($int >> 0x8), 0xFF & $int);
	    imagesetpixel ($image, $x, $y, $color);
	}
    }

    $new_image = imagecreatetruecolor($width * 1.25, $height * 1.25);
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width * 1.25, $height * 1.25, $width, $height);

    //Output image and clean
    imageJPEG($new_image, NULL, 100);
    imagedestroy($image);
    imagedestroy($new_image);
    $chart = ob_get_contents();
    ob_end_clean();

    return $chart;
}

/*
 *  End of file image_helper.php */
/* Location: ./system/application/helpers/image_helper.php */
