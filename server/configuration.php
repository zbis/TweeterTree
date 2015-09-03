<?php
require_once ('informations.php'); 
// Take the post value and check if the hashtag begin with #, if not 
if (isset($_POST['hashtag']) && $_POST['hashtag'] != ''){
	$hashtag = stripslashes(nl2br(htmlentities($_POST['hashtag'])));
	if($hashtag[0] !== '#'){
	 	$hashtag = '#'.$hashtag;
	}

	$informationsFile = INFORMATIONS_FILE;
	$oldHashtag = HASHTAG;

	$fileContent = file_get_contents($informationsFile);
	$fileContent = str_replace($oldHashtag,$hashtag,$fileContent);
	file_put_contents($informationsFile,$fileContent);

	echo "OK";
}else{
}

// return the hashtag
function getHashtag()
{
    return HASHTAG;
}
// return the nb of tweets by day for statistics (7 days)
function dataHashtag()
{
	$count = array();
	$dataFile = file_get_contents('dataHashtag.json');
	$data = json_decode($dataFile);

	foreach($data->data as $nb)
    {
         array_push($count,$nb->count);
    };
	return "["
			."".  $count[0]
			.",".$count[1]
			.",".$count[2]
			.",".$count[3]
			.",".$count[4]
			.",".$count[5]
			.",".$count[6]
			."]";
}
// return the french presentation of dates for statistics (7 days)
function getDays()
{
	$days = array();
	$dataFile = file_get_contents('dataHashtag.json');
	$data = json_decode($dataFile);

	foreach($data->data as $day)
    {
         array_push($days,$day->day);
    };
	return "categories: ["
			."'".$days[0]
			."','".$days[1]
			."','".$days[2]
			."','".$days[3]
			."','".$days[4]
			."','".$days[5]
			."','".$days[6]
			."']";
}
?>