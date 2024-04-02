<?php 
// Database configuration 
define('DB_HOST', 'localhost'); 
define('DB_USERNAME', 'user_administrator_portal_test'); 
define('DB_PASSWORD', '3^AZ=j5uH*ZLutc-port3l'); 
define('DB_NAME', 'db_portal'); 
define('DB_USER_TBL', 'oauth'); 
 
// GitHub API configuration 
define('CLIENT_ID', '58c0e629e0c7fa50a912'); 
define('CLIENT_SECRET', '81ef40b52e37ffd4a33c4c6673d22a6a0ea6f9f2'); 
define('REDIRECT_URL', 'http://englishcorporatelanguage.com/Portal-UTC/OAuthGitHub.php'); 
 
// Start session 
if(!session_id()){ 
    session_start(); 
} 
 
// Include Github client library 
require_once 'OAuthGitHubClient.php'; 
 
// Initialize Github OAuth client class 
$gitClient = new Github_OAuth_Client(array( 
    'client_id' => CLIENT_ID, 
    'client_secret' => CLIENT_SECRET, 
    'redirect_uri' => REDIRECT_URL 
)); 
 
// Try to get the access token 
if(isset($_SESSION['access_token'])){ 
    $accessToken = $_SESSION['access_token']; 
}