<?php

/*

	Helpers

	Custom modules & snippets

*/


function debug( $thing ) {

	ob_start();

	?><pre><?php var_dump($thing); ?></pre><?php

	$output = ob_get_clean();

	echo $output;

}

//Cache Times
define("ONE_DAY", 24*3600);
define("ONE_HOUR", 3600);



function isTesting(){

	if( !isset( $_GET['testing'] ) ) return false;

	return $_GET['testing']===FALSE ? FALSE : filter_var($_GET['testing'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

}

function isFlushing(){

	if( isset( $_GET['flush'] ) ) return true;

	if( !isset( $_GET['cache'] ) ) return false;

	return $_GET['cache']===FALSE ? FALSE : filter_var($_GET['cache'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

}

function isValidUrl( $url ) {
	$parsed_url = parse_url($url);
	return isset( $parsed_url['host'] );
}

function getSSLPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSLVERSION,3);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


function startsWith($haystack, $needle)
{
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}





function validateJson($strJson) {
    $output = json_decode($strJson);

		if(json_last_error() !== JSON_ERROR_NONE) {
			debug( json_last_error() );
			die();
		}

    return $output;
}



//Bootstrap column class
function bootstrapClass($columns_arg) {

	$columns = intval( $columns_arg );

	$min = 1;
	$max = 12;

	//Is between 1 & 12
	if ($columns >= $min && $columns <= $max){
		$bs_col_width = round( 12 / $columns );
	} else {
		$bs_col_width = 1;
	}

	$bs_col_class = 'col-sm-'.$bs_col_width;

	return $bs_col_class;
}



function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function in_object($string, $object){
	foreach( $object as $item => $item_value ){

		if( is_object($item_value) ){
			foreach( $item_value as $thing => $thing_value ){
				if( $thing_value == $string ) return $item;
			}
		} else {
			if( $item == $string ) return $item;
		}

	}

	return false;
}




//Generate a Contrasting color
function getContrastingColor($hexcolor){
    //24ways.org/2010/calculating-color-contrast/

    //Simple
    //return (hexdec(trim($hexcolor,'#')) > 0xffffff/2) ? '323232':'d8d8d8';

    //YIQ
    $hexcolor = trim($hexcolor,'#');//Strip pounds
    $r = hexdec(substr($hexcolor,0,2));
	$g = hexdec(substr($hexcolor,2,2));
	$b = hexdec(substr($hexcolor,4,2));
	$yiq = (($r*299)+($g*587)+($b*114))/1000;
	return ($yiq >= 128) ? '323232' : 'd8d8d8';
}

//Generate average brightness from a file(Good for setting a contrasting color in front of it)
function get_avg_luminance($filename, $num_samples=10) {
    $img = imagecreatefromjpeg($filename);

    $width = imagesx($img);
    $height = imagesy($img);

    $x_step = intval($width/$num_samples);
    $y_step = intval($height/$num_samples);

    $total_lum = 0;

    $sample_no = 1;

    for ($x=0; $x<$width; $x+=$x_step) {
        for ($y=0; $y<$height; $y+=$y_step) {

            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            // choose a simple luminance formula from here
            // http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
            $lum = ($r+$r+$b+$g+$g+$g)/6;

            $total_lum += $lum;

            $sample_no++;
        }
    }

    // work out the average
    $avg_lum  = $total_lum/$sample_no;
    return $avg_lum;
    // assume a medium gray is the threshold, #acacac or RGB(172, 172, 172)
    // this equates to a luminance of 170
}



function cleanPostDate($arg_post_date) {

	//Detect if it's already UTC
	if( true == is_numeric($arg_post_date) &&  0 != $arg_post_date ){

		//Post date is good to go
		$post_date = $arg_post_date;

	} else {//Parse non-UTC date int UTC

		//Try to read post date an time string
		$post_date = strtotime( $arg_post_date );

	}

	//If it's still not valid then stop
	if( false == $post_date || 0 == $post_date ){ return false; }

	//https://codex.wordpress.org/Function_Reference/current_time
	$wordpress_local_time = current_time( 'U' );//Local time in wordpress
	$an_hour = 60 * 60;//60 seconds times 60 minutes
	$a_day = $an_hour * 24;


	//debug( $post_date >= strtotime( '1 hour ago', $wordpress_local_time ) );

	if( $post_date >= strtotime( '-1 hour', $wordpress_local_time ) ){//If less than 1 hour hours
		//Minute Format - 5 min ago

		$minutes_since = round( abs($wordpress_local_time - $post_date) / 60 );

		$output_date = $minutes_since.' mins ago';

	} else if( date('d/m/Y', $post_date ) == date('d/m/Y', $wordpress_local_time) ){//If it's from today
		//Hour Format - 5 hrs ago

		$hours_since = round( abs($wordpress_local_time - $post_date) / $an_hour );

		$output_date = $hours_since.' hrs ago';

	} else if( date('d/m/Y', $post_date ) == date('d/m/Y', $wordpress_local_time - $a_day) ){//If Yesterday

		$output_date = 'Yesterday';

	} else if( date('W/Y', $post_date ) == date('W/Y', $wordpress_local_time) ){//If from this week
		//Format - Sunday

		$output_date = date('l', $post_date );

	} else if( date('Y', $post_date ) == date('Y', $wordpress_local_time) ){//If from this year
		//Format - August 8

		$output_date = date('F j', $post_date);

	} else {//Pretty much if it's older than this year
		//Format - August 8 1992

		$output_date = date('F j Y', $post_date);

	}

	//19 hrs ago(If less than 24 hours)
	//Yesterday(If date is yesterday)
	//2 days ago(If from this week)
	//Aug 3(If from this year)
	//Dec 31st 2015(If all else)

	return $output_date;

}








//Get Youtube ID of current Stream
function makeYoutubeEmbedLink($html){

	//$youtube_id = $youtube_string;

	$youtube_id = VideoUrlParser::get_url_id( $html );

	//debug($debug);

	//If it's a url get the id from url
/*
	if( filter_var($youtube_string, FILTER_VALIDATE_URL) ){
		$youtube_id = getYTID($youtube_string)
	}
*/

	//developers.google.com/youtube/player_parameters#Parameters
	$embed_data['params'] = array(
//		'autoplay'=>'0',
		'rel'=>'0',
		'showinfo'=>'0',
		'modestbranding'=>'1',
		'theme'=>'light',
		'autohide'=>'1'
	);

	$embed_data['id'] = $youtube_id;

	$embed_data['youtube-url'] = 'https://youtube.com/embed/'.$youtube_id.'?'.http_build_query($embed_data['params']);

	$output = $embed_data['youtube-url'];

	return $output;

}


function get_current_url($strip = true) {
    // filter function
    static $filter;
    if ($filter == null) {
        $filter = function($input) use($strip) {
            $input = str_ireplace(array(
                "\0", '%00', "\x0a", '%0a', "\x1a", '%1a'), '', urldecode($input));
            if ($strip) {
                $input = strip_tags($input);
            }

            // or any encoding you use instead of utf-8
            $input = htmlspecialchars($input, ENT_QUOTES, 'utf-8');

            return trim($input);
        };
    }

    return 'http'. (($_SERVER['SERVER_PORT'] == '443') ? 's' : '')
        .'://'. $_SERVER['SERVER_NAME'] . $filter($_SERVER['REQUEST_URI']);
}




function get_current_relative_url($strip = true) {
    // filter function
    static $filter;
    if ($filter == null) {
        $filter = function($input) use($strip) {
            $input = str_ireplace(array(
                "\0", '%00', "\x0a", '%0a', "\x1a", '%1a'), '', urldecode($input));
            if ($strip) {
                $input = strip_tags($input);
            }

            // or any encoding you use instead of utf-8
            $input = htmlspecialchars($input, ENT_QUOTES, 'utf-8');

            return trim($input);
        };
    }

    return $filter($_SERVER['REQUEST_URI']);
}





//https://gist.github.com/astockwell/11055104

/*
**
 * Video Url Parser
 *
 * Parses URLs from major cloud video providers. Capable of returning
 * keys from various video embed and link urls to manipulate and
 * access videos in various ways.
 */
class VideoUrlParser
{
	/**
	 * Determines which cloud video provider is being used based on the passed url.
	 *
	 * @param string $url The url
	 * @return null|string Null on failure to match, the service's name on success
	 */
	public static function identify_service($url)
	{
		if (preg_match('%youtube|youtu\.be%i', $url)) {
			return 'youtube';
		}
		elseif (preg_match('%vimeo%i', $url)) {
			return 'vimeo';
		}
		return null;
	}
	/**
	 * Determines which cloud video provider is being used based on the passed url,
	 * and extracts the video id from the url.
	 *
	 * @param string $url The url
	 * @return null|string Null on failure, the video's id on success
	 */
	public static function get_url_id($url)
	{
		$service = self::identify_service($url);
		if ($service == 'youtube') {
			return self::get_youtube_id($url);
		}
		elseif ($service == 'vimeo') {
			return self::get_vimeo_id($url);
		}
		return null;
	}
	/**
	 * Determines which cloud video provider is being used based on the passed url,
	 * extracts the video id from the url, and builds an embed url.
	 *
	 * @param string $url The url
	 * @return null|string Null on failure, the video's embed url on success
	 */
	public static function get_url_embed($url)
	{
		$service = self::identify_service($url);
		$id = self::get_url_id($url);
		if ($service == 'youtube') {
			return self::get_youtube_embed($id);
		}
		elseif ($service == 'vimeo') {
			return self::get_vimeo_embed($id);
		}
		return null;
	}
	/**
	 * Parses various youtube urls and returns video identifier.
	 *
	 * @param string $url The url
	 * @return string the url's id
	 */
	public static function get_youtube_id($input_url)
	{

		$url = $input_url;

		//Convert Iframe to Link
		if( preg_match('/src="([^"]+)"/', $url, $match) ){
			$url = $match[1];
		}

		$youtube_url_keys = array('v','vi');
		// Try to get ID from url parameters
		$key_from_params = self::parse_url_for_params($url, $youtube_url_keys);
		if ($key_from_params) return $key_from_params;
		// Try to get ID from last portion of url
		return self::parse_url_for_last_element($url);
	}
	/**
	 * Builds a Youtube embed url from a video id.
	 *
	 * @param string $youtube_video_id The video's id
	 * @return string the embed url
	 */
	public static function get_youtube_embed($youtube_video_id, $autoplay = 1)
	{
		$embed = "http://youtube.com/embed/$youtube_video_id?autoplay=$autoplay";
		return $embed;
	}
	/**
	 * Parses various vimeo urls and returns video identifier.
	 *
	 * @param string $url The url
	 * @return string The url's id
	 */
	public static function get_vimeo_id($input_url)
	{
		$url = $input_url;

		//Convert Iframe to Link
		if( preg_match('/src="([^"]+)"/', $url, $match) ){
			$url = $match[1];
		}
		// Try to get ID from last portion of url
		return self::parse_url_for_last_element($url);
	}
	/**
	 * Builds a Vimeo embed url from a video id.
	 *
	 * @param string $vimeo_video_id The video's id
	 * @return string the embed url
	 */
	public static function get_vimeo_embed($vimeo_video_id, $autoplay = 1)
	{
		$embed = "http://player.vimeo.com/video/$vimeo_video_id?byline=0&amp;portrait=0&amp;autoplay=$autoplay";
		return $embed;
	}
	/**
	 * Find the first matching parameter value in a url from the passed params array.
	 *
	 * @access private
	 *
	 * @param string $url The url
	 * @param array $target_params Any parameter keys that may contain the id
	 * @return null|string Null on failure to match a target param, the url's id on success
	 */
	private static function parse_url_for_params($url, $target_params)
	{
		parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_params );
		foreach ($target_params as $target) {
			if (array_key_exists ($target, $my_array_of_params)) {
				return $my_array_of_params[$target];
			}
		}
		return null;
	}
	/**
	 * Find the last element in a url, without any trailing parameters
	 *
	 * @access private
	 *
	 * @param string $url The url
	 * @return string The last element of the url
	 */
	private static function parse_url_for_last_element($url)
	{
		$url_parts = explode("/", $url);
		$prospect = end($url_parts);
		$prospect_and_params = preg_split("/(\?|\=|\&)/", $prospect);
		if ($prospect_and_params) {
			return $prospect_and_params[0];
		} else {
			return $prospect;
		}
		return $url;
	}
}
