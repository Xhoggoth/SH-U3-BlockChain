<?php 
// Include configuration file 
require_once 'OAuthGitHubConfig.php'; 
 
// Include and initialize user class 
require_once 'OAuthGitHubUserClass.php'; 
$user = new User(); 
 
if(isset($accessToken)){ 
    // Get the user profile data from Github 
    $gitUser = $gitClient->getAuthenticatedUser($accessToken); 
     
    if(!empty($gitUser)){ 
        // Getting user profile details 
        $gitUserData = array(); 
        $gitUserData['oauth_uid'] = !empty($gitUser->id)?$gitUser->id:''; 
        $gitUserData['name'] = !empty($gitUser->name)?$gitUser->name:''; 
        $gitUserData['username'] = !empty($gitUser->login)?$gitUser->login:''; 
        $gitUserData['email'] = !empty($gitUser->email)?$gitUser->email:''; 
        $gitUserData['location'] = !empty($gitUser->location)?$gitUser->location:''; 
        $gitUserData['picture'] = !empty($gitUser->avatar_url)?$gitUser->avatar_url:''; 
        $gitUserData['link'] = !empty($gitUser->html_url)?$gitUser->html_url:''; 
        
        // Insert or update user data to the database 
        $gitUserData['oauth_provider'] = 'github'; 
        $userData = $user->checkUser($gitUserData); 
 
        // Storing user data in the session 
        $_SESSION['userData'] = $userData; 
 
        // Render Github profile data 
        $output     = '<h2>Detalles de la cuenta de GitHub</h2>'; 
        $output .= '<div class="ac-data">'; 
        $output .= '<img src="'.$userData['picture'].'">'; 
        $output .= '<p><b>ID:</b> '.$userData['oauth_uid'].'</p>'; 
        $output .= '<p><b>Name:</b> '.$userData['name'].'</p>'; 
        $output .= '<p><b>Login Username:</b> '.$userData['username'].'</p>'; 
        $output .= '<p><b>Email:</b> '.$userData['email'].'</p>'; 
        $output .= '<p><b>Location:</b> '.$userData['location'].'</p>'; 
        $output .= '<p><b>Profile Link:</b> <a href="'.$userData['link'].'" target="_blank">Click to visit GitHub page</a></p>'; 
        $output .= '<p>Logout from <a href="OAuthGitHubLogout.php">GitHub</a></p>'; 
        $output .= '</div>'; 
    }else{ 
        $output = '<h3 style="color:red">Algo ha ido mal, inténtelo de nuevo!</h3>'; 
    }  
}elseif(isset($_GET['code'])){ 
    // Verify the state matches the stored state 
    if(!$_GET['state'] || $_SESSION['state'] != $_GET['state']) { 
        header("Location: ".$_SERVER['PHP_SELF']); 
    } 
     
    // Exchange the auth code for a token 
    $accessToken = $gitClient->getAccessToken($_GET['state'], $_GET['code']); 
   
    $_SESSION['access_token'] = $accessToken; 
   
    header('Location: ./'); 
}else{ 
    // Generate a random hash and store in the session for security 
    $_SESSION['state'] = hash('sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR']); 
     
    // Remove access token from the session 
    unset($_SESSION['access_token']); 
   
    // Get the URL to authorize 
    $authUrl = $gitClient->getAuthorizeURL($_SESSION['state']); 
     
    // Render Github login button 
    $output = '<a href="'.htmlspecialchars($authUrl).'">Login</a>'; 
} 
?>

<div class="container">
    <!-- Display login button / GitHub profile information -->
    <?php echo $output; ?>
</div>