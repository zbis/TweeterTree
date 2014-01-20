<?php
    include('twitterParser.class.php');
    $fromShield = isset($_GET['fromShield']);
    //configuration d'un shield
    //enregistrement d'un shield
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
            $since = get_last_request($shield_id); 
            if(!$since)
                // it's first shield request with this id so we start new session for it
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

    
?>