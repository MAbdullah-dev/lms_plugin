<?php
require_once 'vendor/autoload.php'; 

session_start(); // Start the session

use League\OAuth2\Client\Provider\GenericProvider;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Set up the Azure provider using environment variables
$provider = new GenericProvider([
    'clientId'                => $_ENV['AZURE_CLIENT_ID'],          // Use $_ENV instead of getenv()
    'clientSecret'            => $_ENV['AZURE_CLIENT_SECRET'],      // Use $_ENV instead of getenv()
    'redirectUri'             => $_ENV['AZURE_REDIRECT_URI'],       // Use $_ENV instead of getenv()
    'urlAuthorize'            => 'https://login.microsoftonline.com/' . $_ENV['AZURE_TENANT_ID'] . '/oauth2/v2.0/authorize', // Use $_ENV instead of getenv()
    'urlAccessToken'          => 'https://login.microsoftonline.com/' . $_ENV['AZURE_TENANT_ID'] . '/oauth2/v2.0/token', // Use $_ENV instead of getenv()
    'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
    'scopes'                  => 'openid profile email'
]);

// Check if we have an authorization code
if (!isset($_GET['code'])) {
    // Redirect back to the login page if no code is found
    header('Location: views/login.php');
    exit;
}

// Validate state parameter if you used one
if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

try {
    // Obtain an access token using the authorization code grant
    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Use the access token to fetch the user's profile from Microsoft Graph
    $resourceOwner = $provider->getResourceOwner($accessToken);
    $user = $resourceOwner->toArray();

    // Store user info in session
    $_SESSION['user'] = [
        'name' => $user['displayName'],
        'email' => $user['mail'] ?? $user['userPrincipalName'], // Use mail if available, else fall back to userPrincipalName
    ];

    // Redirect to a landing page or dashboard
    header("Location: views/courses.php");
    exit();

} catch (Exception $e) {
    // Handle errors
    exit('Error during authentication: ' . $e->getMessage());
}
