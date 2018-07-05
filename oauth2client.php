<?php
/**
 * Created by PhpStorm.
 * User: Jane Wang
 * Date: 3/07/2018
 * Time: 3:10 PM
 */

if(empty(session_id())){
    session_start();
}

require_once 'vendor/autoload.php';

$infusionsoft = new \Infusionsoft\Infusionsoft(array(
    'clientId'     => '5wwajnfs5hpfn8emwjqdmyqr',
    'clientSecret' => 'VjvKwJnkBq',
    'redirectUri'  => 'http://localhost/Projects/Teamwork_API/oauth2client.php',
));

// If the serialized token is available in the session storage, we tell the SDK
// to use that token for subsequent requests.
if (isset($_SESSION['token'])) {
    $infusionsoft->setToken(unserialize($_SESSION['token']));
}

// If we are returning from Infusionsoft we need to exchange the code for an
// access token.
if (isset($_GET['code']) and !$infusionsoft->getToken()) {
    $_SESSION['token'] = serialize($infusionsoft->requestAccessToken($_GET['code']));
}

if ($infusionsoft->getToken()) {
    // Save the serialized token to the current session for subsequent requests
    $_SESSION['token'] = serialize($infusionsoft->getToken());
    echo $_SESSION['token'];

} else {
    echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>';
}





