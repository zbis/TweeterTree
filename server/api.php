<?php
include_once('functions.php');
/*
	API Controller Documentation :
	{base_url}/api.php?domain={domain}&shield_id={shield_id}
	
	POST (creat or update) :
		Register shield : 
			{base_url}/api.php?domain=register
			$_POST expected : 
					'shield_id' => TEXT,
					'password' => TEXT,
					'hastags' => TEXT ("#hastag1, #hastag2, [...]")
					'ledCount' => INT,
					'blinking_time' => INT,
		Set blinking_time
			{base_url}/api.php?domain=blinking_time&shield_id={shield_id}
			$_POST expected : 
					'shield_id' => TEXT,
					'password' => TEXT,
					'blinking_time' => INT,
		Set ledCount
			{base_url}/api.php?domain=led_count&shield_id={shield_id}
			$_POST expected : 
					'shield_id' => TEXT,
					'password' => TEXT,
					'led_count' => INT,
		Set hastags
			{base_url}/api.php?domain=hashtags&shield_id={shield_id}
			$_POST expected : 
					'shield_id' => 'TEXT',
					'password' => 'TEXT',
					'hashtags' => TEXT ("#hastag1, #hastag2, [...]")

		//A implémenter : 
		Add hastag
			{base_url}/api.php?domain=hashtag&shield_id={shield_id}
			$_POST expected : 
					'shield_id' => 'TEXT',
					'password' => 'TEXT',
					'hashtag' => TEXT ("#hastag1"),
					'action' => 'add'
		//A implémenter : 
		Remove hastag
			{base_url}/api.php?domain=hashtag&shield_id={shield_id}
			$_POST expected : 
					'shield_id' => 'TEXT',
					'password' => 'TEXT',
					'hastag' => TEXT ("#hastag1"),
					'action' => 'remove'

	GET (read_only):
		$_GET expected for all : {shieldId}
		Hastags : 
			 {base_url}/api.php?domain=hashtags&shield_id={shield_id}
		LedCount : 
			 {base_url}/api.php?domain=led_count&shield_id={shield_id}
		blinkingTime : 
			 {base_url}/api.php?domain=blinking_time&shield_id={shield_id}
		tweetCount : 
			 {base_url}/api.php?domain=tweets_count&shield_id={shield_id}

*/
			 ini_set('display_errors', 1);
$messages = array();
$responses = array();
if(((isset($_GET['shield_id'])) OR (isset($_POST['shield_id']))) && isset($_GET['domain']))
{
	$shieldId = (isset($_GET['shield_id'])) ?  $_GET['shield_id'] : $_POST['shield_id'];
	/*
		Is post request ? 
		Can register a new shield or update existing shield
	*/
	if(isset($_POST) && !empty($_POST))
	{
		switch($_GET['domain']){
			case 'register':
				/* 
					Register a new shield ? 
					Okey, let's check if id isn't already take
				*/
					if(!get_shield($shieldId))
					{
						register_shield($shieldId, $_POST);
						$messages[] =  array('type' =>'success', 'mess' => get_message('registered_shield'));
					} else {
						$messages[] =  array('type' =>'danger', 'mess' => get_error('already_exist'));
					}
				break;
			case 'update':
				$shield = get_shield($shieldId);
				if($shield) {
					if(isset($_POST['password']) && is_authorized($shieldId, $_POST['password']))
					{
						update_shield($shieldId, $_POST);
						$messages[] =  array('type' =>'success', 'mess' => get_message('updated_shield'));
					} else {
						$messages[] =  array('type' =>'danger', 'mess' => get_error('bad_identification'));
					}
				} else {
					$messages[] =  array('type' =>'danger', 'mess' => get_error('shield_not_found'));	
				}

				break;
			default :
				/* 
					Update existing shield ? 
					Okey let's check permissions
				*/
					if(isset($_POST['password']) && is_authorized($shieldId, $_POST['password']))
					{
						$functionName = $_GET['action'].$_GET['domain'];
						if(function_exists($functionName))
						{
							$functionName($shieldId, $_POST[$_GET['domain']]);
						} else {
							$messages[] = array('type' =>'danger', 'mess' => get_error('action_unknow', $functionName));
						}
					} else {
						$messages[] = array('type' =>'danger', 'mess' => get_error('identification_failed'));
					}
				break;
		}
	// On get request we don't need password 
	} else {
		$functionName = 'get_'.$_GET['domain'];
		if(function_exists($functionName))
		{

			$item = $functionName($shieldId);
			if($item or $_GET['domain'] == 'tweets_count')
			{
				switch($_GET['domain']) {
					case 'shield':
						if(isset($_GET['password'])) //but if password send, it's for user modifications so need identification
						{
							if(is_authorized($shieldId, $_GET['password']))
							{
								$responses = array('autorisation' => true);
								$messages[] =  array('type' =>'success', 'mess' => get_message('identification_success'));
							} else {
								$responses = array('autorisation' => false);
								$messages[] =  array('type' =>'danger', 'mess' => get_error('identification_failed'));
							}
						}
					default :
						$responses[$_GET['domain']] = $item;
				}
			}
			else {
				$messages[] =  array('type' =>'danger', 'mess' => get_error($_GET['domain'].'_not_found'));
			}
		} else {
			$messages[] =  array('type' =>'danger', 'mess' => get_error('action_unknow', $functionName));
		}
	}
}
$json = array();
/* display errors*/
foreach($messages as $key => $message)
{
    $json['notifications'][] = array('typealert' => $message['type'], 'message' => $message['mess']);
} 
foreach($responses as $key => $response)
{
	$json[$key] = $response;
}
if(!empty($responses) && isset($_GET['card'])) {
	$xml = '<shield>';
	if(is_array($responses[$_GET['domain']] )) {
		foreach($responses[$_GET['domain']] as $key => $resp)
		{
			if(!is_int($key)) 
			{
				$xml .= "<$key>$resp</$key>";
			}
		}
	} else {
		$xml .= "<".$_GET['domain'].">".$responses[$_GET['domain']]."</".$_GET['domain'].">";
	}
	$xml .= '</shield>';
	echo $xml;
} else {
	echo json_encode($json); 
}