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
                    CURLOPT_TIMEOUT => 10);
  
  
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
        //using expanded_url here to ensure accurate URL is used
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

  //Count words in a string and return most common
  function mostCommonWord($string){
    $lowerCase = strtolower($string);
    $lowerCase = preg_replace("/http[^\s]+/", '', $lowerCase);
    $textExplode = preg_split("/[\s!@#$%&*)(+=}{\\:;\",.?<>]/", $lowerCase, -1, PREG_SPLIT_NO_EMPTY);
    $word_counts = array_count_values($textExplode);

    //find max count
    $max = max($word_counts);
    $top = array_keys($word_counts, $max);

    return $top;
  }

  $twitterData = json_decode($json);
  $allText = '';

  //Parse through decoded JSON and insert into the DOM 
  echo "<div id='timeline'>";
  foreach($twitterData as $tweet){
    echo "<div class='tweet'>";
    
    //Gather desired tweet information
    $text = $tweet->text;
    $screen_name = $tweet->user->screen_name;
    $name = $tweet->user->name;
    $profile_image_url = $tweet->user->profile_image_url;

    //Add text to allText for counting later on
    $allText = $allText.' '.$text;
    
    //Add links where appropriate
    $text = insertEntity($text, $tweet);

    //Insert into DOM
    echo "<a class=\"profile_image\" href=\"http://twitter.com/".$screen_name."\" title=\"".$screen_name."\" target=\"_blank\"><img src=\"".$profile_image_url."\" alt=\"".$name."\" /></a>"; 
    echo "<div class=\"tweet_content\">";
    echo "<a class=\"screen_name\" href=\"http://twitter.com/".$screen_name."\" title=\"".$name."\" target=\"_blank\">".$screen_name."</a>";
    echo "<div class=\"name\" >@".$name."</div>"; 
    echo "<div class=\"tweet_text\">".$text."</div>";  
    echo "</div></div>";

  }
  echo "</div>";

  $most_common_word = mostCommonWord($allText);
  
  foreach($most_common_word as $word){
    $most_common_string .= "<a href='https://twitter.com/search?q=%23".$word."' target='_blank'>".$word."</a> ";
  }

   echo "</div>
         <h2><a href=\"#\">Question 6</a></h2>
         <div class=\"question\">
            <h4 class=\"prompt\">Using the results from the previous question, determine the most commonly used words. What is the scalability of this algorithm? Would this alrogrithm still work if you were parsing billions of tweets?</h4>
            <p>MOST COMMON WORD(S): $most_common_string</p>";
?>

  <p>
    The algorithm I used is very simple - it uses the pre-built PHP functions strtolower(), preg_replace(), preg_split(), array_count_values(), 
    max(), and array_keys(). Array_count_values() has to loop over each element in $textExplode() 
    and compare it with each key that has already been added to $word_counts. If the key does not exist, it is made and the value is set to 1.
    Otherwise, the value of the existing key is incremented by 1.<br><br>

    I was going to write this function with arSort() but its algorithm runs in O(nlogn) so I worked around it.<br><br>
    
    The algorithm employed in the code has an asymptotic running time of O(n) and should scale linearly. It would scale very well though I'm not sure how
    PHP would handle over a billion elements in an array - probably not well. Here is the methodology:

    <ol>
      <li>Start out with an n-long array of all words, with duplicates, $textExplode. Also have an associative array (dictionary) $word_counts.
      <li>Loop over each word in $textExplode and increment the $value in $word_counts[word] using array_count_values().
      <li>Set $max equal to the maximum value (count) with max($word_counts).
      <li>Get the keys (words) of the elements in $word_counts whose values match $max using and place them in new array $top using array_keys($word_counts, $max).
      <li>Return $top and echo it out.
    </ol>

    $top consists of the most frequent words as they each occurred the maximum amount of times (this accounts for situations
    when multiple words have the same highest $word_counts[$word] value).<br><br>
    The cost is O(n) as we had to run over the n-long array $textExplode once and $word_counts twice. This is the theoretic lower bound and scales very well.
  </p>