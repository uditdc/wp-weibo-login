<?php 

require_once 'sdk/saetv2.ex.class.php';
require_once 'weibo-login.php';

$client_id = '1686503323';
$client_secret = 'b20453508497ba25e482368aa35ff9df';
$callback_url = "http://www.desirews.com/authorized";

/**
 * 
 *
*/

$o = new SaeTOAuthV2($client_id, $client_secret);

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = $callback_url;

	try{
		$token = $o->getAccessToken('code', $keys);
		echo $token;
	}catch(OAuthException $e){

	}
}

if ($token){
	$_SESSION['token'] = $token;
	setcookie('weibologin_'.$o->client_id, http_build_query($token));
	$weibo_login = new Weibo_Login;
	$weibo_login->do_login($token);
}