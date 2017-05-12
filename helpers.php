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


use phpFastCache\CacheManager;

// Setup File Path on your config files
CacheManager::setDefaultConfig(array(
    "path" => __DIR__ . '/cache', // or in windows "C:/tmp/"
));

// In your class, function, you can call the Cache
global $InstanceCache;
$InstanceCache = CacheManager::getInstance('files');

//Cache Times
define("ONE_HOUR", 3600);
define("ONE_DAY", 24 * ONE_HOUR);
define("ONE_YEAR", ONE_DAY * 365);



function isTesting(){

	if( !isset( $_GET['testing'] ) ) return false;

	return $_GET['testing']===FALSE ? FALSE : filter_var($_GET['testing'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

}

function isFlushing( $id = false ){

	if( !isset( $_GET['flush'] ) ) return false;

	$flushing_default = ( empty( $_GET['flush'] ) );

	$flushing_id = ( $id == $_GET['flush'] );


	// Default Flush( not specific )
	if( !$id && $flushing_default ) {
		return true;
	// Specific flush keyword
} else if( $flushing_id ) {
		return true;
	} else {
		return false;
	}

}

function isValidUrl( $url ) {
	$parsed_url = parse_url($url);
	return isset( $parsed_url['host'] );
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
