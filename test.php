<?php
/**
 * Created by PhpStorm.
 * User: Jane Wang
 * Date: 5/07/2018
 * Time: 4:14 PM
 */
// - - - - - - - - - - - - - - - - - - - - - -
// Include function.php file
// - - - - - - - - - - - - - - - - - - - - - -
require_once 'vendor/autoload.php';

$infusionsoft = new \Infusionsoft\Infusionsoft(array(
    'clientId'     => '5wwajnfs5hpfn8emwjqdmyqr',
    'clientSecret' => 'VjvKwJnkBq',
    'redirectUri'  => 'http://localhost/Projects/Teamwork_API/test.php',
));
if (isset($_GET['code']) and !$infusionsoft->getToken()) {
    $myTokenObject = $infusionsoft->requestAccessToken($_GET['code']);
}

if ($infusionsoft->getToken()) {
    // Save the serialized token to the current session for subsequent requests
    $myTokenObject = $infusionsoft->getToken();
    $infusionsoft->setToken($myTokenObject);

    $contact = $infusionsoft->contacts('xml')->load("177231", array("FirstName", "LastName"));
    print_r($contact);

}else {
    echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>';
}
