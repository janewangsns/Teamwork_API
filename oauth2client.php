<?php
/**
 * Created by PhpStorm.
 * User: Jane Wang
 * Date: 3/07/2018
 * Time: 3:10 PM
 */
include "function.php";

if (!isset($code)) {
    $code = $_GET['code'];
}

$tokenInfo = getToken($code);

$accessToken = $tokenInfo["access_token"];
$refreshToken = $tokenInfo["refresh_token"];




