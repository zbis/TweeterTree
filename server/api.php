<?php
include_once('functions.php');
/*
	API Controller Documentation :
	{base_url}/domain/action/
	
	POST (creat or update) :
		Register shield : 
			{base_url}/register
			$_POST expected : 
					'shieldId' => TEXT,
					'password' => TEXT,
					'hastags' => TEXT ("#hastag1, #hastag2, [...]")
					'ledCount' => INT, 
					'blinkingTime' => INT,
		Set blinkingTime
			{base_url}/blinktime/set
			$_POST expected : 
					'shieldId' => TEXT,
					'password' => TEXT,
					'blinkingTime' => INT,
		Set ledCount
			{base_url}/ledcount/set
			$_POST expected : 
					'shieldId' => TEXT,
					'password' => TEXT,
					'ledCount' => INT,
		Set hastag
			{base_url}/hastag/set
			$_POST expected : 
					'shieldId' => 'shieldid',
					'password' => 'passphrase',
					'hastags' => TEXT ("#hastag1, #hastag2, [...]")
		Add hastag
			{base_url}/hastag/add
			$_POST expected : 
					'shieldId' => 'shieldid',
					'password' => 'passphrase',
					'hastag' => TEXT ("#hastag1")
		Remove hastag
			{base_url}/hastag/remove
			$_POST expected : 
					'shieldId' => 'shieldid',
					'password' => 'passphrase',
					'hastag' => TEXT ("#hastag1")

	GET (read_only):
		$_GET expected for all : {shieldId}
		Hastags : 
			 {base_url}/hastags/get/{shieldId}
		LedCount : 
			 {base_url}/led/get/{shieldId}
		blinkingTime : 
			 {base_url}/blinktime/get/{shieldId}
		lasttweet : 
			 {base_url}/lasttweet/get/{shieldId}

*/
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
			if($item)
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
						$responses[] = $item;
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
	$json['items'][$key] = $response;
}
echo json_encode($json);