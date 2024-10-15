<?php

use League\OAuth2\Client\Provider\GenericProvider;
class OAuthProviderService {
     public function __construct() {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); 
        $dotenv->load();

        if (empty($_ENV['AZURE_CLIENT_ID']) || empty($_ENV['AZURE_CLIENT_SECRET']) || empty($_ENV['AZURE_REDIRECT_URI']) || empty($_ENV['AZURE_TENANT_ID'])) {
            die("Environment variables are not set correctly.");
        }
    }
    public function getProvider() {
        return new GenericProvider([
            'clientId'                => $_ENV['AZURE_CLIENT_ID'],
            'clientSecret'            => $_ENV['AZURE_CLIENT_SECRET'],
            'redirectUri'             => $_ENV['AZURE_REDIRECT_URI'],
            'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
            'scopes'                  => $_ENV['AZURE_SCOPES'],
        ]);
    }
}
