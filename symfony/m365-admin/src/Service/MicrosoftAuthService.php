<?php

namespace App\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;


class MicrosoftAuthService
{
    private Microsoft $provider;

    public function __construct(
        private string          $microsoftClientId,
        private string          $microsoftClientSecret,
        private string          $microsoftDefaultGroupId,
        private string          $microsoftCallbackUrl,
        private Client          $client,
        private LoggerInterface $logger,
        private GraphService    $graphService
    )
    {
        $this->provider = new Microsoft([
            'clientId' => $this->microsoftClientId,
            'clientSecret' => $this->microsoftClientSecret,
            'redirectUri' => $this->microsoftCallbackUrl,
            'urlAuthorize' => 'https://login.windows.net/common/oauth2/authorize',
            'urlAccessToken' => 'https://login.windows.net/common/oauth2/token',
            'urlResourceOwnerDetails' => 'https://outlook.office.com/api/v1.0/me',
        ]);
    }

    public function getLoggedInUser(string $code): array
    {
        $token = $this->getAccessToken($code);

        $response = $this->client->request('GET', 'https://graph.microsoft.com/v1.0/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data['error'])) {
            $this->logger->error('Error fetching logged-in user: ' . $data['error']);
            throw new \RuntimeException('Error fetching logged-in user: ' . $data['error']);
        } else {

            return $data;
            $userId = $data['id'];

            if ($userId && $this->microsoftDefaultGroupId) {
                $result = $this->graphService->addUserToGroupByIds($userId, $this->microsoftDefaultGroupId);
            }

            if ($result) {
                $this->logger->info('User added to group. User: ' . $userId . ' Group' . $this->microsoftDefaultGroupId);
                return $result;
            } else {
                $this->logger->error('Error adding user to group. User: ' . $userId . ' Group' . $this->microsoftDefaultGroupId);
                return false;
            }
        }
    }

    public function addUserToGroupByIds($userId)
    {
        return $this->graphService->addUserToGroupByIds($userId, $this->microsoftDefaultGroupId);
    }

    public function getAccessToken(string $code): AccessToken
    {
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);
        return $token;
    }
}
