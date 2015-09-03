<?php

	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

	ini_set('display_errors', 1);

	require_once('informations.php');
	require_once('TwitterAPIExchange.php');

	// URL To get back the tweets
	$url = 'https://api.twitter.com/1.1/search/tweets.json';

	$params = '?q='.urlencode(str_replace(', ', ' ', HASHTAG));

	$since = file_get_contents("tweet.txt");
	if(isset($since)) $params.='&since_id='.$since;

	$twitter = new TwitterAPIExchange();
	$retour = $twitter	->setGetfield($params)
				 		->buildOauth($url, 'GET')
				 		->performRequest();
	$retour = json_decode($retour);

	if (isset($retour->statuses)) {
      $tweets = $retour->statuses;
    }

    $nbTweet = count($tweets);
    $value = 'STOP';
    
    /*
    * Part to modify the json file if the date change and update the count of tweets in link with the hashtag
    */
    $days = array();
    $count = array();
 	$dataFile = file_get_contents('dataHashtag.json');
	$data = json_decode($dataFile);

	foreach($data->data as $day)
    {
         array_push($days,$day->day);

    };
    foreach($data->data as $nb)
    {
         array_push($count,$nb->count);
    };
    if($days[6] == date('d/m/y')){
    	$data->data[6]->count += $nbTweet;
    }else{
    	$data->data[0]->day = $data->data[1]->day;
    	$data->data[1]->day = $data->data[2]->day;
    	$data->data[2]->day = $data->data[3]->day;
    	$data->data[3]->day = $data->data[4]->day;
    	$data->data[4]->day = $data->data[5]->day;
    	$data->data[5]->day = $data->data[6]->day;
		$data->data[6]->day = date('d/m/y');

    	$data->data[0]->count = $data->data[1]->count;
    	$data->data[1]->count = $data->data[2]->count;
    	$data->data[2]->count = $data->data[3]->count;
    	$data->data[3]->count = $data->data[4]->count;
    	$data->data[4]->count = $data->data[5]->count;
    	$data->data[5]->count = $data->data[6]->count;
    	$data->data[6]->count = 0;
    }
    file_put_contents('dataHashtag.json', json_encode($data));
    //////////////////////////////////////////////////////////
    if($nbTweet > 0) {
    	$lastTweetId = $tweets[0]->id_str;
    	file_put_contents("tweet.txt", $lastTweetId);
    	$value = 'TWEET';
    }

    echo $value;

?>
