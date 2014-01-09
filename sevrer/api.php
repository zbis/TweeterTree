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
$errors = array();

if(((isset($_GET['shield_id']) && $_GET['shield_id'] =! null) OR (isset($_POST['shield_id']) && $_POST['shield_id'] =! null)) && isset($_GET['domain']))
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
					} else {
						$errors[] = get_error('already_exist');
					}
				break;
			default :
				/* 
					Update existing shield ? 
					Okey let's check permissions
				*/
					if(isset($_POST['password'])) && is_authorized($shieldId, $_POST['password']))
					{
						$functionName = $_GET['action'].$_GET['domain'];
						if(function_exists($functionName))
						{
							$functionName($shieldId, $_POST[$_GET['domain']]);
						} else {
							$errors[] = get_error('action_unknow', $functionName);
						}
					} else {
						$errors[] = get_error('identification_failed');
					}
				break;
		}
	// On get request we don't need password 
	} else {
		$functionName = 'get_'.$_GET['domain'];
		if(function_exists($functionName))
		{
			echo '<'.$functionName($shieldId).'>';
		} else {
			$errors[] = get_error('action_unknow', $functionName);
		}
	}
}

/* display errors*/
if(isset($errors[0]))
{
	echo "<error>";
	foreach($errors as $error)
	{
		echo " ".$error;
	}
}