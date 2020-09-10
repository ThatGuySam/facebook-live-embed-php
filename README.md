# Facebook Live Embed

## Introduction

> Gets current or latest live stream from a Facebook page

[Here's a demo!](http://fblive.samcarlton.com/)

Based on [youtube-live-embed](https://github.com/iacchus/youtube-live-embed)

## Code Samples

### Get Embed code
This generates the embed code for you with respect to width and height of the livestream(ie: Portrait live streams from phones)
```php
<div class="embed-container">
    <?= $FacebookLive->embedCode() ?>
</div>
```

### Get Embed URL
This just get the embed url so you can use it more custom ways
```php
<iframe src="<?= $FacebookLive->getEmbedAddress() ?>" frameborder="0" allowfullscreen></iframe>
```

## Installation

### 1. Install

```sh
$ git clone https://github.com/ThatGuySam/facebook-live-embed
$ cd facebook-live-embed
$ composer install
```

### 2. Configure
- Go to https://developers.facebook.com/
- Create a new app and get the App ID and the App Secret
- Get the link for the page you want to pull live streams from
- In the install folder rename config-sample.php to just config.php
- Edit config.php and add your App ID, App Secret, and Facebook Page Link respectively


### 3. Include PHP Class
```php
require_once __DIR__ . '/GetFacebookLiveStream.php';

```

### 4. Configure Live instance
```php
$FacebookLive = new GetFacebookLiveStream([
  'facebook_page' => $fb_page,     // Facebook Page URL or ID
  'app_id' => FB_APP_ID,           // Facebook App ID
  'app_secret' => FB_APP_SECRET,   // Facebook App Secret
  'cache_stream_for' => 60,        // How long to cache request for in seconds
]);
```

### 5. Place in template
This generates the embed code for you with respect to width and height of the livestream(ie: Portrait live streams from phones)
```php
<div class="embed-container">
    <?= $FacebookLive->embedCode() ?>
</div>
```


### 5.1 Alternative custom use in template (Bootstrap)
```php
<div class="fb-live-embed embed-container">

  <div class="embed-responsive embed-responsive-16by9 is-<?= $FacebookLive->embed_orientation ?>" style="padding-top: <?= $FacebookLive->embed_ratio_percent; ?>"  >
    <iframe
      src="<?= $FacebookLive->getEmbedAddress() ?>"
      scrolling="no"
      frameborder="0"
      webkitallowfullscreen="1" mozallowfullscreen="1" allowfullscreen="1"
      ></iframe>
  </div>

</div>
```

## Request Private Consulting on this project
<p align="center">
  <a href="https://otechie.com/ThatGuySam?ref=badge"><img src="https://api.otechie.com/consultancy/ThatGuySam/badge.svg" alt="Hire Sam Carlton"></a>
</p>
