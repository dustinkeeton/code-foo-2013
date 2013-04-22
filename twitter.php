<?php 
  function buildBaseString($baseURI, $method, $params) {
      $r = array();
      ksort($params);
      foreach($params as $key=>$value){
          $r[] = "$key=" . rawurlencode($value);
      }
      return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
  }

  function buildAuthorizationHeader($oauth) {
      $r = 'Authorization: OAuth ';
      $values = array();
      foreach($oauth as $key=>$value)
          $values[] = "$key=\"" . rawurlencode($value) . "\"";
      $r .= implode(', ', $values);
      return $r;
  }

  $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
  $query_url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=IGN&count=40";

  $oauth_access_token = "ENTER YOURS HERE";
  $oauth_access_token_secret = "ENTER YOURS HERE";
  $consumer_key = "ENTER YOURS HERE";
  $consumer_secret = "ENTER YOURS HERE";

  $oauth = array( 'screen_name' => 'IGN', 
                  'count' => 40,
                  'oauth_consumer_key' => $consumer_key,
                  'oauth_nonce' => time(),
                  'oauth_signature_method' => 'HMAC-SHA1',
                  'oauth_token' => $oauth_access_token,
                  'oauth_timestamp' => time(),
                  'oauth_version' => '1.0');

   $oauth_header = array( 'oauth_consumer_key' => $consumer_key,
                          'oauth_nonce' => time(),
                          'oauth_signature_method' => 'HMAC-SHA1',
                          'oauth_token' => $oauth_access_token,
                          'oauth_timestamp' => time(),
                          'oauth_version' => '1.0');

  $base_info = buildBaseString($url, 'GET', $oauth);
  $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
  $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
  $oauth['oauth_signature'] = $oauth_signature;

  // Make Requests
  $header = array(buildAuthorizationHeader($oauth), 'Expect:');
  $options = array( CURLOPT_HTTPHEADER => $header,
                    //CURLOPT_POSTFIELDS => $postfields,
                    CURLOPT_HEADER => false,
                    CURLOPT_URL => $url .'?screen_name=IGN&count=40',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false);
  
  $feed = curl_init();
  curl_setopt_array($feed, $options);
  $json = curl_exec($feed);
  curl_close($feed);
  

  $jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($json, TRUE)), RecursiveIteratorIterator::SELF_FIRST);
  foreach ($jsonIterator as $key => $val){
    if(is_array($val)) {
      echo "$key:\n";
    }
    else{
      echo "$key => $val\n";
    }
  }
?>