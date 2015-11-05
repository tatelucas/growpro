<?php
require_once("themevast/twitteroauth/twitteroauth.php"); //Path to twitteroauth library
class Themevast_Lastesttweet_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {	

		$_config = Mage::getStoreConfig('lastesttweet/general');
		$tweets = array();
		if(isset($_config['twitteruser'])){
			$twitteruser 		= $_config['twitteruser'];
			$notweets 			= isset($_config['notweets']) ? $_config['notweets'] : 5;
			$consumerkey 		= isset($_config['consumerkey']) ? $_config['consumerkey']  : '';
			$consumersecret 	= isset($_config['consumersecret']) ? $_config['consumersecret'] : '';
			$accesstoken 		= isset($_config['accesstoken']) ? $_config['accesstoken'] : '';
			$accesstokensecret 	= isset($_config['accesstokensecret']) ? $_config['accesstokensecret'] : '';
			 
			function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
			  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
			  return $connection;
			}
			  
			$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
			 
			$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);			
		}
		echo json_encode($tweets);
    }
}

