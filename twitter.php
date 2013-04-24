<?php 

  //builds base string for oauth based on sorting and encoding requriements
  function buildBaseString($baseURI, $method, $params) {
      $r = array();
      ksort($params);
      foreach($params as $key=>$value){
          $r[] = "$key=" . rawurlencode($value);
      }
      return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
  }

  //builds authorization header for oauth
  function buildAuthorizationHeader($oauth) {
      $r = 'Authorization: OAuth ';
      $values = array();
      foreach($oauth as $key=>$value)
          $values[] = "$key=\"" . rawurlencode($value) . "\"";
      $r .= implode(', ', $values);
      return $r;
  }

  //base URI for API call
  $baseURI = "https://api.twitter.com/1.1/statuses/user_timeline.json";

  //INSERT OWN VALUES HERE FROM https://dev.twitter.com/apps
  $oauth_access_token = "ENTER YOURS HERE";
  $oauth_access_token_secret = "ENTER YOURS HERE";
  $consumer_key = "ENTER YOURS HERE";
  $consumer_secret = "ENTER YOURS HERE";

  //oauth parameters with query parameters
  $oauth = array( 'screen_name' => 'IGN', 
                  'count' => 40,
                  'include_rts' => 1,
                  'oauth_consumer_key' => $consumer_key,
                  'oauth_nonce' => time(),
                  'oauth_signature_method' => 'HMAC-SHA1',
                  'oauth_token' => $oauth_access_token,
                  'oauth_timestamp' => time(),
                  'oauth_version' => '1.0');

  //create oauth signature
  $base_info = buildBaseString($baseURI, 'GET', $oauth);
  $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
  $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
  $oauth['oauth_signature'] = $oauth_signature;

  // Make Requests
  $header = array(buildAuthorizationHeader($oauth), 'Expect:');
  $options = array( CURLOPT_HTTPHEADER => $header,
                    //CURLOPT_POSTFIELDS => $postfields,
                    CURLOPT_HEADER => false,
                    CURLOPT_URL => $baseURI .'?screen_name=IGN&count=40&include_rts=1', //adds query string to request
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_FAILONERROR => true,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOTP_TIMEOUT => 10);
  
  
  $feed = curl_init();
  curl_setopt_array($feed, $options);
  $json = curl_exec($feed);
  curl_close($feed);
  
  /*
  *
  *
  * 
  *
  */

  //Uses Twitter API Entities to replace link text with actual links
  function insertEntity($text, $tweet) {
    if (isset($tweet->entities->urls) || isset($tweet->entities->hashtags) || isset($tweet->entities->user_mentions)) {
      foreach ($tweet->entities->urls as $url) {
        //using expanded_url here to ensure correct URL is used
        $expanded_url = $url->expanded_url;                                             
        $replace = "<a href='".$expanded_url."' target='_blank'>".$url->display_url."</a>";
        $text = str_replace($url->url, $replace, $text);
      }

      foreach ($tweet->entities->hashtags as $hashtag) {
        $find = '#'.$hashtag->text;
        $replace = "<a href='https://twitter.com/search?q=%23".$hashtag->text."' target='_blank'>".$find."</a>";
        $text = str_replace($find, $replace, $text);
      }

      foreach ($tweet->entities->user_mentions as $user_mention) {
        $find = '@'.$user_mention->screen_name;
        $replace = "<a href='http://twitter.com/".$user_mention->screen_name."' target='_blank'>".$find."</a>";
        $text = str_replace($find, $replace, $text);
      }
    }
    return $text;
  }

  $twitterData = json_decode($json);
  $allText = array();

  //Parse through decoded JSON and insert into the DOM 
  echo "<div id='timeline'>";
  foreach($twitterData as $tweet){
    echo "<div class='tweet'>";
    
    //Gather desired tweet information
    $text = $tweet->text;
    $screen_name = $tweet->user->screen_name;
    $name = $tweet->user->name;
    $profile_image_url = $tweet->user->profile_image_url;

    //Add text to "clean" text to allText array for counting later on
    $lowerCase = strtolower($text);
    $lowerCase = preg_replace("/http[^\s]+/", '', $lowerCase);
    $textExplode = preg_split("/[\s!@#$%&*)(+=}{\\:;\",.?<>]/", $lowerCase);
    $allText = array_merge($allText, $textExplode);

    //Add links where appropriate
    $text = insertEntity($text, $tweet);

    // NOT BEING USED - timestamp from API was Berlin/Europe for some reason
    // $timestamp = strtotime($tweet->created_at);
    // $date = date('D', $timestamp);
    // $time = date('g:ia e', $timestamp);

    //Insert into DOM
    echo "<a class=\"profile_image\" href=\"http://twitter.com/".$screen_name."\" title=\"".$screen_name."\" target=\"_blank\"><img src=\"".$profile_image_url."\" alt=\"".$name."\" /></a>"; 
    echo "<div class=\"tweet_content\">";
    echo "<a class=\"screen_name\" href=\"http://twitter.com/".$screen_name."\" title=\"".$name."\" target=\"_blank\">".$screen_name."</a>";
    echo "<div class=\"name\" >@".$name."</div>"; 
    echo "<div class=\"tweet_text\">".$text."</div>";  
    echo "</div></div>";

  }
  echo "</div>";

  //Count words and return most common
  $word_counts = array_count_values($allText);
  arSort($word_counts);
  $garbage = array_shift($word_counts);     //array always contains invalid first element. array_filter() does not solve issue
  $most_common_word = key($word_counts);
  echo "<div>MOST COMMON WORD: \"<a href='https://twitter.com/search?q=%23".$most_common_word."' target='_blank'>".$most_common_word."</a>\"</div>";
  ?>