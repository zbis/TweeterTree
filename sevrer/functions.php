<?php 
/* 
	START 	I - Application configuration 
*/

function get_defaut_configuration()
{
	return array(
		'hastags' 		=> '#allonsy',
		'ledCount' 		=> 16, 
		'blinkingTime' 	=> 1000,
		);
}

function get_errors_message()
{
	return array(
		'already_exist' 			=> 'Cette ID de shield est déjà prise',
		'identification_failed' 	=> '',
		'action_unknow' 			=> ' %f'
		);
}

/* 
	END 	I - Application configuration
	START 	II - Generique functions 

--------------	II.a Database interractions 

*/
function getbdd()
{
	try {
	    // Nouvel objet de base SQLite 
    $db_handle = new PDO('sqlite:lighting_tree.sqlite');
    // Quelques options
    $db_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //first creat table if not exist
    $db_handle->exec("CREATE TABLE IF NOT EXISTS requests (ID INTEGER PRIMARY KEY, shield_id TEXT, last_request TEXT);");
	$db_handle->exec("CREATE TABLE IF NOT EXISTS shields (ID INTEGER PRIMARY KEY, shield_id TEXT, password TEXT, hashtags TEXT, led_count INT, blikning_time INT);");
	retun $db_handle;
	catch (Exception $e) {
        if($fromShield)
            {
                die("500");//return error code
            }
        die('Exception :'.$e);
    }
}
function do_query($query)
{
	$db_handle = getbdd();
    return $db_handle->query($query);
}

function get_shield($shieldId)
{
	$result = do_query("SELECT * FROM shields WHERE (shield_id = '$shieldId') LIMIT 1");
    //this shield already requested API
    return $result->fetch();
}

function prepare_query($query, $shieldId)
{
    	$db_handle = getbdd();
    	$sth = $db_handle->prepare($query);
    	$sth->bindParam(':shield_id', $shieldId);
        return $sth;
}

/* 
--------------	II.b Tools
*/
function merge_configs($userconfig)
{
	$defaultconfig = get_default_config();
	$config = array();
	foreach($defaultconfig as $key => $params) {
		$config[$key] = (isset($userconfig[$key]) && $userconfig[$key] != '') ? $userconfig[$key]: $params;
	}
	return $config;
}
function get_error($key)
{
	$errors = get_errors_message();
	reutrn $errors[$key];
}
/* 
	END 	II 	- Generics function
	START 	III - Getter and setters for all database column 
*/

/*
	shields.hastags
*/
function get_hashtags($shieldId)
{
	$shield = get_shield($shieldId);
    return isset($shield['hashtags'])? $shield_request['hashtags'] : false;
}
function add_hashtag($shieldId, $hastag)
{
	$hastags = get_hashtags($shieldId);
	if($hastags != '')
	{
		$hastag = $hastags.', '.$hastag
	}
	set_hashtags($shieldId, $hastag);
}

function set_hashtags($shieldId, $hastags)
{
    try {
    	$sth = prepare_query("UPDATE shields SET hastags=:hastags WHERE shield_id=:shield_id", $shieldId);
        $sth->bindParam(':hastags', $hastags);     
        $sth->execute();
    } catch (Exception $e) {
        die('Exception :'.$e);
    }
}
function remove_hastag($shieldId, $hastag)
{
	$hastagsStr = get_hashtags($shieldId);
	$hastags = str_getcsv($hastagsStr, ',');
	if(in_array($hastag, $hastags)) {
		$key = array_search($hastag, $hashtags);
		unset($hashtags[$key]);
	}
	set_hashtags($shieldId, implode(",", $hashtags));
}

/*
	shields.blikning_time
*/
function get_blinking_time($shieldId)
{
	$shield = get_shield($shieldId);
    return isset($shield['blikning_time'])? $shield_request['blikning_time'] : false;
}

function set_blinking_time($shieldId, $blinkingTime)
{
    try {
    	$sth = prepare_query("UPDATE shields SET blikning_time=:blikning_time WHERE shield_id=:shield_id", $shieldId);
        $sth->bindParam(':blikning_time', $blinkingTime);
        $sth->execute();
    } catch (Exception $e) {
        die('Exception :'.$e);
    }
}

/*
	shields.led_count
*/
function get_led_count($shieldId)
{
	$shield = get_shield($shieldId);
    return isset($shield['led_count'])? $shield_request['led_count'] : false;
}
function set_led_count($shieldId, $ledCount)
{
    try {
    	$sth = prepare_query("UPDATE shields SET led_count=:led_count WHERE shield_id=:shield_id");
        $sth->bindParam(':led_count', $ledCount);
        $sth->execute();
    } catch (Exception $e) {
        die('Exception :'.$e);
    }
}

/*
	requests.last_request
*/

function get_last_request($shieldId)
{
    $result = do_query("SELECT * FROM requests WHERE (shield_id = '$shieldId') LIMIT 1");
    //this shield already requested API
    $shield_request = $result->fetch();
    return isset($shield_request['last_request'])? $shield_request['last_request'] : false;
}

function set_last_request($shieldId, $lastTweetId)
{
    try {
    	$sth = prepare_query("UPDATE requests SET last_request=:last_request WHERE shield_id=:shield_id", $shieldId);
	    $sth->bindParam(':last_request', $lastTweetId);
        return $sth->execute();
    } catch (Exception $e) {
        die('Exception :'.$e);
    }
}

/* 
	END 	III - Getter and setters for all database column 
	START 	IV 	- Core functions
*/

/* Check couple password/shield */

function is_authorized($shieldId, $password)
{
	$shield = get_shield($shieldId);
	return ($shied['password'] = md5($password));
}

/*
	Save a new shield with defaut configuration if it's necessary
*/
function register_shield($shieldId, $userconfig = array())
{
	$config = merge_configs($userconfig);
    try {
    	$sth = prepare_query( "INSERT INTO shields (shield_id, password, hashtags, led_count, blikning_time) VALUES (:shield_id, :password ,:hashtags, :led_count, :blikning_time)");
	    $sth->bindParam(':hashtags', $config['hastags']);
	    $sth->bindParam(':led_count', $config['ledCount']);
	    $sth->bindParam(':blikning_time', $config['blikningTime']);
	    $sth->bindParam(':password', md5($config['password']));
        return $sth->execute();
    } catch (Exception $e) {
        die('Exception :'.$e);
    }
}

/*
	Get tweet count for specific shield
	Use Twitter API
*/
function get_tweets_count($shieldId)
{
	include_once('twitterParser.class.php');
	$twitter = new twitterParser();
    $hastags = get_hashtags($shieldId);

    $query = 'q='.urlencode($hastags);

    //Add last tweet ID on request, if it's on database
    if(isset($since)) 
    {
        $query.='&since_id='.$since;
    }
    // get all tweets
    $tweets = $twitter->getTweets($query);

    //Have new tweet ? Save last Tweet ID on database for this shield
    if(sizeof($tweets)>0){
    	$lastTweetId = $tweets[0]->getID();
    	set_last_request($shieldId, $lastTweetId);
    } 
	reutrn $tweets;
}


?>