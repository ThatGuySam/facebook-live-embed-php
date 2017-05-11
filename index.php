<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/GetFacebookLiveStream.php';

 ?>
<!doctype html>
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

	<style type="text/css">

		html {
			margin: 0;
			height: 100%;
			overflow: hidden;
		}

		iframe {
      width: 640px;
      height: 360px;
      
			position: absolute;
			left:0;
			right:0;
			bottom:0;
			top:0;
			border:0;
		}

	</style>

</head>
<body>
  <iframe frameborder="0" src="https://player.vimeo.com/video/216945799?title=0&byline=0&portrait=0&color=d8d8d8&api=1&player_id=frame" data-php-live="" data-current-url="archive" data-live="" data-archive="https://player.vimeo.com/video/216945799?title=0&byline=0&portrait=0&color=d8d8d8&api=1&player_id=frame" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
</body>
</html>
