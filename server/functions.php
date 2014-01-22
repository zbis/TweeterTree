<?php 
/* 
	START 	I - Application configuration 
*/

function get_defaut_configuration()
{
	return array(
		'hastags'         => '#allonsy',
		'led_count'       => 16, 
		'blinking_time'   => 1000,
		);
}

function get_errors_message()
{
	return array(
		'already_exist' 			=> 'Cette ID de shield est déjà prise',
		'identification_failed' 	=> 'Erreur lors de l\'indentification du shield',
		'action_unknow' 			=> 'Action "%s" inconnue',
        'shield_not_found'          => 'Shield introuvable. Vous pouvez l\'enregistrer en cliquant sur le bouton "Créer mon arbre"',
        'blinking_time_not_found'   => 'Temps de clignottement introuvable',
        'led_count_not_found'       => 'Nombre de leds introuvable',
        'hashtags_not_found'        => 'Hashtag(s) introuvable(s)',
        'tweets_count_not_found'    => 'Nombre de tweets introuvables'
		);
}
function get_success_message()
{
    return array(
        'registered_shield' => 'Votre carte a bien été enregistrée',
        'updated_shield' => 'Votre carte a bien été mise à jour. Merci de réinitialiser votre arbre.',
        'identification_success' => 'Votre carte est correctement identifiée'
        );
}
function get_notifications_message()
{
    return array(
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
    	$db_handle->exec("CREATE TABLE IF NOT EXISTS shields (ID INTEGER PRIMARY KEY, shield_id TEXT, password TEXT, hashtags TEXT, led_count INT, blinking_time INT);");
    	return $db_handle;
    }
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

function get_shield($shieldId, $withPass = false)
{
    $select = "shield_id, blinking_time, led_count, hashtags";
    if($withPass) {
        $select .= ", password ";
    }
	$result = do_query("SELECT $select FROM shields WHERE (shield_id = '$shieldId') LIMIT 1");
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
	$defaultconfig = get_defaut_configuration();
	foreach($defaultconfig as $key => $params) {
		$userconfig[$key] = (isset($userconfig[$key]) && $userconfig[$key] != '') ? $userconfig[$key]: $params;
	}
	return $userconfig;
}
function get_error($key, $var = false)
{
    $errors = get_errors_message();
    $error  = $errors[$key];
    if($var) {
        $error = sprintf($error, $var);
    }
	return $error;
}
function get_message($key)
{
    $messages = get_success_message();
    return $messages[$key];
}
function default_conf($key)
{
    $dconf = get_defaut_configuration();
    return $dconf[$key];
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
    return isset($shield['hashtags'])? $shield['hashtags'] : false;
}
function add_hashtag($shieldId, $hastag)
{
	$hastags = get_hashtags($shieldId);
	if($hastags != '')
	{
		$hastag = $hastags.', '.$hastag;
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
	shields.blinking_time
*/
function get_blinking_time($shieldId)
{
	$shield = get_shield($shieldId);
    return isset($shield['blinking_time'])? $shield['blinking_time'] : false;
}

function set_blinking_time($shieldId, $blinkingTime)
{
    try {
    	$sth = prepare_query("UPDATE shields SET blinking_time=:blinking_time WHERE shield_id=:shield_id", $shieldId);
        $sth->bindParam(':blinking_time', $blinkingTime);
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
    return isset($shield['led_count'])? $shield['led_count'] : false;
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
	$shield = get_shield($shieldId, true);
	return ($shield['password'] == md5($password));
}

/*
	Save a new shield with defaut configuration if it's necessary
*/
function register_shield($shieldId, $userconfig = array())
{
	$config = merge_configs($userconfig);
    try {
        $password = md5($config['password']);
    	$sth = prepare_query( "INSERT INTO shields (shield_id, password, hashtags, led_count, blinking_time) VALUES (:shield_id, :password ,:hashtags, :led_count, :blinking_time)", $shieldId);
	    $sth->bindParam(':hashtags', $config['hastags']);
	    $sth->bindParam(':led_count', $config['led_count']);
	    $sth->bindParam(':blinking_time', $config['blinking_time']);
	    $sth->bindParam(':password', $password);
        return $sth->execute();
    } catch (Exception $e) {
        die('Exception :'.$e);
    }
}
function update_shield($shieldId, $userconfig = array())
{
    $config = merge_configs($userconfig);
    try {
        $password = md5($config['password']);
        $sth = prepare_query( "UPDATE shields SET hashtags = :hashtags, led_count = :led_count, blinking_time = :blinking_time WHERE shield_id = :shield_id AND password = :password", $shieldId);
        $sth->bindParam(':hashtags', $config['hastags']);
        $sth->bindParam(':led_count', $config['led_count']);
        $sth->bindParam(':blinking_time', $config['blinking_time']);
        $sth->bindParam(':password', $password);
        $shield =  $sth->execute();
        set_last_request($shieldId, null);
        //also, if we update shield, reinitialize led count.
        return $shield;
    } catch (Exception $e) {
        die('Exception :'.$e);
    }
}
function get_cheat_tweets_count($shieldId)
{
    return rand(0,15);
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
	return $tweets;
}


?>