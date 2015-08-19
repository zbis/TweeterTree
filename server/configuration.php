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
	echo "NOK";
}

// return the hashtag
function getHashtag()
{
    return HASHTAG;
}
?>