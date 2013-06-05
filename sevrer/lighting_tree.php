<?php
    include('twitterParser.class.php');
    $fromShield = isset($_GET['fromShield']);
    try {
    // Nouvel objet de base SQLite 
    $db_handle = new PDO('sqlite:lighting_tree.sqlite');
    // Quelques options
    $db_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //first creat table if not exist
    $results = $db_handle->exec("CREATE TABLE IF NOT EXISTS requests (ID INTEGER PRIMARY KEY, shield_id TEXT, last_request TEXT);");
    // second retrieve Shield identifiant
    // Select all data from memory db messages table 
    //TODO login/Password identification
        if(isset($_GET['shield_id']) && $_GET['shield_id']!='')
        {
            $shield_id = strtolower($_GET['shield_id']);
            $result = $db_handle->query("SELECT * FROM requests WHERE (shield_id = '$shield_id') LIMIT 1");
            $shield_request = $result->fetch();
            if($shield_request) //this shield already requested API
            {
                $since = $shield_request['last_request'];
            } else {// it's first shield request with this id so we start new session for it
                 $sql = "INSERT INTO requests (shield_id) VALUES (:shield_id)";
                 $sth = $db_handle->prepare($sql);
                 $sth->bindParam(':shield_id', $shield_id);
                 $sth->execute();
            }
        } else {
            if($fromShield)
            {
                die("403");//return error code
            }
            throw new Exception("shield_id is missing", 1);
        }
    } catch (Exception $e) {
        if($fromShield)
            {
                die("500");//return error code
            }
        die('Exception :'.$e);
    }

    /*
     * Configuration
     */
    $hastag = '#sachezle';  

    // Twitter parser request twitter API and return Tweets Objects
    $twitter = new twitterParser();
    
    $query = 'q='.urlencode($hastag);

    //Add last tweet ID on request, if it's on database
    if(isset($since)) 
    {
        $query.='&since_id='.$since;
    }
    // get all tweets
    $tweets = $twitter->getTweets($query);

    //Have new tweet ? Save last Tweet ID on database for this shield
    if(sizeof($tweets)>0){
        try {
            $lastTweetId = $tweets[0]->getID();
            $sql = "UPDATE requests SET last_request=:last_request WHERE shield_id=:shield_id";
            $sth = $db_handle->prepare($sql);
            $sth->bindParam(':last_request', $lastTweetId); //first Tweet on table is last Tweet returned by API
            $sth->bindParam(':shield_id', $shield_id);
            $sth->execute();
        } catch (Exception $e) {
            die('Exception :'.$e);
        }
    } 
	echo "<".sizeof($tweets).">";
?>