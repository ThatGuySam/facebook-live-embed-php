<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/GetFacebookLiveStream.php';

if( isset( $_GET['stream'] ) ){
  $fb_page = $_GET['stream'];
} else {
  $fb_page = FB_PAGE;
}

$FacebookLive = new GetFacebookLiveStream([
  'facebook_page' => $fb_page,
  'app_id' => FB_APP_ID,
  'app_secret' => FB_APP_SECRET,
  'cache_stream_for' => 60,
]);

?><!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

	<title>Live Stream</title>

	<!-- Cache busting -->
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.4.1/css/bulma.min.css">

	<style type="text/css">

		html {
			margin: 0;
			height: 100%;
			/*overflow: hidden;*/
		}

	</style>

</head>
<body>

<section class="hero is-dark">
  <div class="container">

    <div class="columns">
      <div class="column is-8 is-offset-2">

        <?= $FacebookLive->embedCode() ?>

      </div>
    </div>

  </div>
</section>

<section class="section">
  <div class="container">

      <div class="media">
        <br>
        <div class="content">
          <strong>Is it live</strong><br><br>
          <?= debug( $FacebookLive->isLive() ); ?>
        </div>
      </div>

      <div class="media">
         <div class="content">
           <strong>What is it</strong><br><br>
           <?= debug( $FacebookLive->loaded_video_description ); ?>
         </div>
      </div>

      <div class="media">
         <div class="content">
           <strong>When was it started</strong><br><br>
           <?= debug( $FacebookLive->loaded_video_published_at ); ?>
         </div>
      </div>

      <div class="media">
         <div class="content">
           <strong>What orientation is it</strong><br><br>
           <?= debug( $FacebookLive->embed_orientation ); ?>
         </div>
      </div>

      <div class="media">
         <div class="content">
           <strong>Embed URL</strong> <a href="<?= $FacebookLive->getEmbedAddress() ?>" target="_blank" class="button is-small">View</a>
           <br><br>
           <?= debug( $FacebookLive->getEmbedAddress() ); ?>
         </div>
      </div>

      <div class="media">
         <div class="content">
           <strong>Video URL</strong> <a href="<?= $FacebookLive->loaded_video_url ?>" target="_blank" class="button is-small">View</a>
           <br><br>
           <?= debug( $FacebookLive->loaded_video_url ); ?>
         </div>
      </div>

      <div class="media">
         <div class="content">
           <strong>Thumb URL</strong> <a href="<?= $FacebookLive->loaded_video_thumb_default ?>" target="_blank" class="button is-small">View</a>
           <br><br>
           <?= debug( $FacebookLive->loaded_video_thumb_default ); ?>
         </div>
      </div>

  </div>
</section>

</body>
</html>
