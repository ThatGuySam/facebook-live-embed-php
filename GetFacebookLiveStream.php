<?php

// Like https://github.com/iacchus/youtube-live-embed

class GetFacebookLiveStream
{
	public $pageID;
	public $APP_Id;
	public $APP_Secret;

	public $jsonResponse; // pure server response
	public $objectResponse; // response decoded as object
	public $arrayRespone; // response decoded as array

	public $isLive; // true if there is a live streaming at the channel

	public $queryData; // query values as an array
	public $getAddress; // address to request GET
	public $getQuery; // data to request, encoded

	public $queryString; // Address + Data to request

	public $part;
	public $eventType;
	public $type;

	public $default_embed_width;
	public $default_embed_height;
	public $default_ratio;

	public $embed_code; // contain the embed code
	public $embed_autoplay;
	public $embed_width;
	public $embed_height;

	public $live_video_id;
	public $live_video_title;
	public $live_video_description;

	public $live_video_publishedAt;

	public $live_video_thumb_default;
	public $live_video_thumb_medium;
	public $live_video_thumb_high;

	public $channel_title;

	public function __construct($PageID, $APP_Id, $APP_Secret, $autoQuery = true)
	{
		$this->pageID = $PageID;
		$this->APP_Id = $APP_Id;
		$this->APP_Secret = $APP_Secret;
		$this->access_token = $this->getFacebookToken()->objectResponse->access_token;

		$this->requestPath = '/' . $this->pageID . '/videos?fields=live_status';

		$this->part = "id,snippet";
		$this->eventType = "live";
		$this->type = "video";

		$this->getAddress = "https://graph.facebook.com/oauth/access_token?";

		$this->default_embed_width = "560";
		$this->default_embed_height = "315";
		$this->default_ratio = $this->default_embed_width / $this->default_embed_height;

		$this->embed_width = $this->default_embed_width;
		$this->embed_height = $this->default_embed_height;

		$this->embed_autoplay = true;

		if($autoQuery == true) { $this->queryIt(); }
	}

	public function queryIt()
	{

		$this->fb = new Facebook\Facebook([
		  'app_id' => $this->APP_Id,
		  'app_secret' => $this->APP_Secret,
		  'default_graph_version' => 'v2.9',
	  ]);

		$this->fb->setDefaultAccessToken( $this->access_token );

		return $this->getFacebookRequest();

		// $this->queryData = array(
		// 	"part" => $this->part,
		// 	"pageID" => $this->pageID,
		// 	"eventType" => $this->eventType,
		// 	"type" => $this->type,
		// 	"key" => $this->APP_Id,
		// );
		// $this->getQuery = http_build_query($this->queryData); // transform array of data in url query
		// $this->queryString = $this->getAddress . $this->getQuery;
		//
		// $this->jsonResponse = file_get_contents($this->queryString); // pure server response
		// $this->objectResponse = json_decode($this->jsonResponse); // decode as object
		// $this->arrayResponse = json_decode($this->jsonResponse, TRUE); // decode as array
		//
		// $this->isLive();
		// if($this->isLive)
		// {
		// 	$this->live_video_id = $this->objectResponse->items[0]->id->videoId;
		// 	$this->live_video_title = $this->objectResponse->items[0]->snippet->title;
		// 	$this->live_video_description = $this->objectResponse->items[0]->snippet->description;
		//
		// 	$this->live_video_published_at = $this->objectResponse->items[0]->snippet->publishedAt;
		// 	$this->live_video_thumb_default = $this->objectResponse->items[0]->snippet->thumbnails->default->url;
		// 	$this->live_video_thumb_medium = $this->objectResponse->items[0]->snippet->thumbnails->medium->url;
		// 	$this->live_video_thumb_high = $this->objectResponse->items[0]->snippet->thumbnails->high->url;
		//
		// 	$this->channel_title = $this->objectResponse->items[0]->snippet->channelTitle;
		// 	$this->embedCode();
		// }
	}

	public function requestFacebookToken()
	{

		$token = new stdClass();
		$token->birthday = date('U');

		$this->queryData = array(
			"client_id" => $this->APP_Id,
			"client_secret" => $this->APP_Secret,
			"grant_type" => "client_credentials",
		);

		$this->getQuery = http_build_query($this->queryData); // transform array of data in url query
		$this->queryString = $this->getAddress . $this->getQuery;

		$token->jsonResponse = file_get_contents($this->queryString); // pure server response
		$token->objectResponse = json_decode($token->jsonResponse); // decode as object
		$token->arrayResponse = json_decode($token->jsonResponse, TRUE); // decode as array

		return $token;
	}

	public function getFacebookToken($key = 'default')
	{

		global $InstanceCache;

		$is_flushing = false;//isFlushing();

		/**
		 * Try to get $products from Caching First
		 * product_page is "identity keyword";
		 */
		$CachedString = $InstanceCache->getItem($key . "_fb_token");

		if ( is_null($CachedString->get()) || $is_flushing !== false ) {

			$requested_data = $this->requestFacebookToken();

			$CachedString->set($requested_data)->expiresAfter( ONE_YEAR );//in seconds, also accepts Datetime
			$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities

	    $output = $CachedString->get();

		} else {

			$output = $CachedString->get();

		}

		//debug( $CachedString->getTtl() / 3600 );

		return $output;
	}


	public function requestFacebookResource( $path )
	{

		try {
		  $response = $this->fb->get( $path );
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		return $response;

	}


	public function getFacebookRequest()
	{

		global $InstanceCache;

		$is_flushing = isFlushing();

		$key = filter_var( $this->requestPath, FILTER_SANITIZE_STRING );

		$CachedString = $InstanceCache->getItem("fb_request_" . $key);

		if ( is_null($CachedString->get()) || $is_flushing !== false ) {

			$requested_data = $this->requestFacebookResource( $this->requestPath );

			$CachedString->set($requested_data)->expiresAfter( 60 );//in seconds, also accepts Datetime
			$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities

	    $output = $CachedString->get();

		} else {

			$output = $CachedString->get();

		}

		//debug( $CachedString->getTtl() / 3600 );

		return $output;
	}


	public function isLive($getOrNot = false)
	{
		if($getOrNot==true)
		{
			$this->queryIt();
		}

		$live_items = count($this->objectResponse->items);

		if($live_items>0)
		{
			$this->isLive = true;
			return true;
		}
		else
		{
			$this->isLive = false;
			return false;
		}
	}

	public function setEmbedSizeByWidth($width, $refill_code = true)
	{
		$ratio = $this->default_embed_width / $this->default_embed_height;
		$this->embed_width = $width;
		$this->embed_height = $width / $ratio;

		if( $refill_code == true ) { $this->embedCode(); }
	}

	public function setEmbedSizeByHeight($height, $refill_code = true)
	{
                $ratio = $this->default_embed_width / $this->default_embed_height;
                $this->embed_height = $height;
                $this->embed_width = $height * $ratio;

		if( $refill_code == true ) { $this->embedCode(); }
	}

	public function embedCode()
	{
		$autoplay = $this->embed_autoplay ? "?autoplay=1" : "";

		$this->embed_code = <<<EOT
<iframe
	width="{$this->embed_width}"
	height="{$this->embed_height}"
	src="//www.youtube.com/embed/{$this->live_video_id}{$autoplay}"
	frameborder="0"
	allowfullscreen>
</iframe>
EOT;

		return $this->embed_code;
	}
}

?>
