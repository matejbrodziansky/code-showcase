<?php

namespace App\Provider;

use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Stevenmaguire\OAuth2\Client\Provider\MicrosoftResourceOwner;


class MicrosoftProvider extends AbstractProvider
{

    private Client $graph;

    protected function getBaseUrl()
    {
        return 'https://login.microsoftonline.com/common/oauth2/v2.0/';
    }

    protected function getDefaultScopes()
    {
        return ['User.Read'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                isset($data['error_description']) ? $data['error_description'] : $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new MicrosoftResourceOwner($response);
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://graph.microsoft.com/v1.0/me';
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->getBaseUrl() . 'authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->getBaseUrl() . 'token';
    }

}
