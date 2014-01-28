<?php
require('tweet.class.php');
require_once ('twitteroauth/twitteroauth.php'); 

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
      $tweets = array();
      $response = $this->apiRequest($q);
      if ($response->statuses)
      {
        return sizeof($response->statuses);
      }
      return 0;
    }
    private function apiRequest($q)
    {
        $consumer_key='9XUDqNvf3KqIdVCAcYR0QQ'; //consumer key
        $consumer_secret='BZNFx7kT0OUmXoVmfCTi9x6yV69oqF9Wo9NCDkubE'; // consumer secret
        $oauth_token = '402058546-i68KnsgGGPu0fsN8Om9vPGuzveJETQYJ5AiX2K5w'; //oAuth Token
        $oauth_token_secret = 'On9AdJzZ4Wf3iUfyvZ2gIKsMOfgrnlf50Y5XEZWJqlg5B'; //oAuth Token Secret
         
        //creation de l'objet
        $connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
        $requete = 'https://api.twitter.com/1.1/search/tweets.json?'.$q;
        $response = $connection->get($requete);
        return $response;
    }
  function getTweets($q)
  {
    $tweets = array();
    $response = $this->apiRequest($q);
    if ($response->statuses)
    {
      $tweets = $response->statuses;
    }
    return $tweets;
  }
}