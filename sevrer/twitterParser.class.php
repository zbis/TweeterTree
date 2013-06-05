<?php
require('tweet.class.php');
class twitterParser
{
    private $realNamePattern = '/\((.*?)\)/';
    private $searchURL = 'http://search.twitter.com/search.atom?lang=fr&';

    public function __construct($options = array()) {
        foreach($options as $key => $option)
        {
            if(isset($this->$key)){
                $this->$key = $option;
            }
        }
    }
    public function countTweets($q)
    {
        $tweets = 0;
        $response = $this->apiRequest($q);
        if ($response !== FALSE)
        {
           $xml = simplexml_load_string($response);
           $tweets = 0;
           for($i=0; $i<count($xml->entry); $i++)
           {
               $tweets++;
           }
         }
         return $tweets;
    }
    private function apiRequest($q)
    {
        // get the seach result
        $ch= curl_init($this->searchURL . $q);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, (isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT'] : ''));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
  function getTweets($q)
  {
    $tweets = array();
    $response = $this->apiRequest($q);
    if ($response !== FALSE)
    {
      $xml = simplexml_load_string($response);
      $tweets = array();
      
      for($i=0; $i<count($xml->entry); $i++)
      {
        $crtEntry = $xml->entry[$i];

        $account  = (string)$crtEntry->author->uri;
        $image    = (string)$crtEntry->link[1]->attributes()->href;

        $msg    = str_replace('<a href=', '<a target="_blank" href=', (string)$crtEntry->content);
        $time = strtotime($crtEntry->published);
        $urlpart = explode("/",parse_url((string)$crtEntry->link[0]->attributes()->href, PHP_URL_PATH));
        $id = $urlpart[sizeof($urlpart)-1];
        // name is in this format "acountname (Real Name)"
        preg_match($this->realNamePattern, $crtEntry->author->name, $matches);
        $author = $matches[1];

        $tweets[] = new Tweet($account, $author, $msg, $image, $time, $id);
      }
    }

    return $tweets;
  }
}