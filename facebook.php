<?php
	session_start(); // starting
	require_once 'lib/Facebook/autoload.php'; // change path as needed
	//autoload


	$fb = new \Facebook\Facebook([
	  'app_id' => 'your app id',//app id
	  'app_secret' => 'your app secret', //password
	  'default_graph_version' => 'v2.10',
	  //'default_access_token' => '{access-token}', // optional
	]);

	$helper = $fb->getRedirectLoginHelper();
	// var_dump($helper);

	try {
		//criando uma variável global

		if (isset($_SESSION['face_access_token'])) {
			/*se existir o face_access ele acessa a variavel global */

			$accessToken = $_SESSION['face_access_token'];
			// $accessToken = $helper->getAccessToken();
			
		}else{
			//se não ele cria um novo token
			$accessToken = $helper->getAccessToken();
		}
  

		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		//end first try catch


	$permissions = ['email']; // Optional permissions

	if (! isset($accessToken)) {
	//se não existir o acces token
		$url_login = 'http://localhost/login/face.php';
		$loginUrl = $helper->getLoginUrl($url_login, $permissions);

	}else{

		$url_login = 'http://localhost/login/face.php';
		$loginUrl = $helper->getLoginUrl($url_login, $permissions);

		//user logged

		if (isset($_SESSION['face_access_token'])) {
			// Since all the requests will be sent on behalf of the same user,
			// we'll set the default fallback access token here.
			$fb->setDefaultAccessToken($_SESSION['face_access_token']);
		}else{
			//user is not logged
			// The OAuth 2.0 client handler helps us manage access tokens
			$_SESSION['face_access_token'] = (string) $accessToken;
			$oAuth2Client = $fb->getOAuth2Client();
			// Long Lived AccessToken will take more time of life
			//I just replace the variable fb_access_token  instead i put my global
			$_SESSION['face_access_token'] = 
			$oAuth2Client->getLongLivedAccessToken($_SESSION['face_access_token']);
			//requisition of informations
			$fb->setDefaultAccessToken($_SESSION['face_access_token']);

		}

				try {
			  // Returns a `Facebook\FacebookResponse` object
			  $response = $fb->get('/me?fields=name,picture,email');
			  $user = $response->getGraphUser();
			  // var_dump($user);
			  // exit();
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
			  echo 'Graph returned an error: ' . $e->getMessage();
			  exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
			  echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  exit;
		}//end last if else


	}//end first if else

	// $user = $response->getGraphUser();

// echo 'Name: ' . $user['name'];
// OR
// echo 'Name: ' . $user->getName();
 ?>