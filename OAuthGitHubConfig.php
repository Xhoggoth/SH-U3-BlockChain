<?php 
// Database configuration 
define('DB_HOST', 'localhost'); 
define('DB_USERNAME', ''); 
define('DB_PASSWORD', ''); 
define('DB_NAME', 'db_portal'); 
define('DB_USER_TBL', 'oauth'); 
 
// GitHub API configuration 
define('CLIENT_ID', ''); 
define('CLIENT_SECRET', ''); 
define('REDIRECT_URL', ''); 
 
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
