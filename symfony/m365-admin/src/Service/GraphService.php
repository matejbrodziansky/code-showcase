<?php

namespace App\Service;

use App\Data\Helper\GraphHelper;
use http\Client;
use http\Exception\RuntimeException;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Psr\Log\LoggerInterface;
use Microsoft\Graph\Generated\Models\ReferenceCreate;


class GraphService
{

    private GraphServiceClient $graphServiceClient;


    public function __construct(
        private string          $microsoftClientId,
        private string          $microsoftClientSecret,
        private string          $microsoftTenantId,
        private GraphHelper     $graphHelper,
        private LoggerInterface $logger,


    )
    {
        $tokenRequestContext = new ClientCredentialContext(
            $this->microsoftTenantId,
            $this->microsoftClientId,
            $this->microsoftClientSecret
        );

        $this->graphServiceClient = new GraphServiceClient($tokenRequestContext, ['https://graph.microsoft.com/.default']);

    }

    public function getAllUsers(): array
    {
        try {
            $users = $this->graphServiceClient->users()->get()->wait()->getValue();

            return $users;
        } catch (\Throwable $e) {
            $this->logger->error('Error fetching users: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAllUsersWithGroups(): array
    {
        try {
            $users = $this->graphServiceClient->users()->get()->wait()->getValue();

            foreach ($users as $user) {
                $userId = $user->getId();
                $userGroups = $this->graphServiceClient->users()->byUserId($userId)->memberOf()->get()->wait()->getValue();;
                $userGroupsIds = $this->graphHelper->extractGroupIds($userGroups);

                $user->groups = $userGroups;
                $user->groupsIds = $userGroupsIds;

            }
            return $users;
        } catch (\Throwable $e) {
            $this->logger->error('Error fetching users with groups: ' . $e->getMessage());
            throw new \RuntimeException('Error fetching users with groups', 0, $e);
        }
    }

    public function getAllGroups(): array
    {
        try {
            $groups = $this->graphServiceClient->groups()->get()->wait()->getValue();

            return $groups;
        } catch (\Throwable $e) {
            $this->logger->error('Error fetching groups: ' . $e->getMessage());
            throw new \RuntimeException('Error fetching groups', 0, $e);
        }

    }

    public function addUserToGroupByIds(string $userId, string $groupId): bool
    {
        try {
            $requestBody = new ReferenceCreate();
            $requestBody->setOdataId('https://graph.microsoft.com/v1.0/directoryObjects/' . $userId);

            $this->graphServiceClient->groups()->byGroupId($groupId)->members()->ref()->post($requestBody)->wait();

            return true;

        } catch (\Throwable $e) {
            $this->logger->error('Failed to add user to group in Microsoft Graph API: ' . $e->getMessage());
            return false;
        }
    }

    public function addUserToGroup(string $userEmail, string $groupName): bool
    {
        $users = $this->getAllUsers();
        $groups = $this->getAllGroups();

        $userId = $this->graphHelper->userExists($users, $userEmail);
        $groupId = $this->graphHelper->groupExists($groups, $groupName);

        $messageUser = null;
        if ($userId === false) {
            $messageUser = 'User not found. ';
        }

        $messageGroup = null;
        if ($groupId === false) {
            $messageGroup = 'Group not found. ';
        }

        $message = $messageUser . $messageGroup;


        if ($message) {
            $this->logger->error('Failed to add user to group in Microsoft Graph API: ' . $message);
            return false;
        }

        try {
            $requestBody = new ReferenceCreate();
            $requestBody->setOdataId('https://graph.microsoft.com/v1.0/directoryObjects/' . $userId);

            $this->graphServiceClient->groups()->byGroupId($groupId)->members()->ref()->post($requestBody)->wait();

            return true;

        } catch (\Throwable $e) {
            $this->logger->error('Failed to add user to group in Microsoft Graph API: ' . $e->getMessage());
            throw new \RuntimeException('Failed to add user to group in Microsoft Graph API', 0, $e);

        }
    }

    public function removeUserFromGroup(string $userEmail, string $groupName): bool
    {

        $users = $this->getAllUsers();
        $groups = $this->getAllGroups();

        $userId = $this->graphHelper->userExists($users, $userEmail);
        $groupId = $this->graphHelper->groupExists($groups, $groupName);

        $messageUser = '';
        if ($userId === false) {
            $messageUser = 'User not found. ';
        }

        $messageGroup = '';
        if ($groupId === false) {
            $messageGroup = 'Group not found. ';
        }

        $message = $messageUser . $messageGroup;

        if ($message !== '') {
            $this->logger->error('Failed to remove user from group in Microsoft Graph API: ' . $message);
            return $message;
        }

        try {
            $this->graphServiceClient->groups()->byGroupId($groupId)->members()->byDirectoryObjectId($userId)->ref()->delete()->wait();

            return true;

        } catch (\Throwable $e) {
            $this->logger->error('Failed to add user to group in Microsoft Graph API: ' . $e->getMessage());
            throw new \RuntimeException('Failed to add user to group in Microsoft Graph API', 0, $e);

        }
    }

    public function removeUserFromGroupByIds(string $userId, string $groupId): bool
    {
        try {
            $this->graphServiceClient->groups()->byGroupId($groupId)->members()->byDirectoryObjectId($userId)->ref()->delete()->wait();

            return true;

        } catch (\Throwable $e) {
            $this->logger->error('Error removing user from group: ' . $e->getMessage());
            return false;
        }

    }

    public function removeUserFromAllGroups(string $userId): bool
    {

        try {
            $userGroups = $this->graphServiceClient->users()->byUserId($userId)->memberOf()->get()->wait()->getValue();
            $userGroupsIds = $this->graphHelper->extractGroupIds($userGroups);

            foreach ($userGroupsIds as $groupId) {

                $this->graphServiceClient->groups()->byGroupId($groupId)->members()->byDirectoryObjectId($userId)->ref()->delete()->wait();
            }

            return true;
        } catch (\Throwable $e) {
            $this->logger->error('Error removing user from all groups: ' . $e->getMessage());
            throw new \RuntimeException('Error removing user from all groups', 0, $e);

        }
    }

    public function getAllGroupsMembers(array $groups): array
    {
        foreach ($groups as $key => $group) {
            $groupId = $group->getId();
            $groupMembers = $this->graphServiceClient->groups()->byGroupId($groupId)->members()->get()->wait()->getValue();
            $groups[$key]->members = $groupMembers;
        }
        return $groups;
    }

    public function clearAllGroups(): bool
    {
        try {
            $groups = $this->graphServiceClient->groups()->get()->wait()->getValue();

            foreach ($groups as $group) {
                $groupId = $group->getId();
                $members = $this->graphServiceClient->groups()->byGroupId($groupId)->members()->get()->wait()->getValue();

                foreach ($members as $member) {
                    $memberId = $member->getId();
                    $this->graphServiceClient->groups()->byGroupId($groupId)->members()->byDirectoryObjectId($memberId)->ref()->delete()->wait();
                }
            }

            return true;

        } catch (\Throwable $e) {
            $this->logger->error('Error clearing all groups: ' . $e->getMessage());
            throw new \RuntimeException('Error clearing all groups', 0, $e);
        }
    }

    public function clearGroupById(string $groupId): bool
    {
        try {
            $members = $this->graphServiceClient->groups()->byGroupId($groupId)->members()->get()->wait()->getValue();

            foreach ($members as $member) {
                $memberId = $member->getId();
                $this->graphServiceClient->groups()->byGroupId($groupId)->members()->byDirectoryObjectId($memberId)->ref()->delete()->wait();
            }

            return true;

        } catch (\Throwable $e) {
            $this->logger->error('Error clearing group: ' . $e->getMessage());
            throw new RuntimeException('Error clearing group', 0, $e);
        }
    }

    public function clearGroupByName(string $groupName): bool
    {
        try {
            $groups = $this->graphServiceClient->groups()->get()->wait()->getValue();

            $groupId = $this->graphHelper->groupExists($groups, $groupName);

            if ($groupId === false) {
                $this->logger->error('Group not found');
                return false;
            }

            $members = $this->graphServiceClient->groups()->byGroupId($groupId)->members()->get()->wait()->getValue();

            foreach ($members as $member) {
                $memberId = $member->getId();
                $this->graphServiceClient->groups()->byGroupId($groupId)->members()->byDirectoryObjectId($memberId)->ref()->delete()->wait();
            }

            return true;

        } catch (\Throwable $e) {
            $this->logger->error('Error clearing group: ' . $e->getMessage());
            throw new RuntimeException('Error clearing group', 0, $e);
        }
    }

    public function getAccessTokenFromCode(string $code, string $redirectUri): ?string
    {
        $client = new Client();

        $url = 'https://login.microsoftonline.com/' . $this->microsoftTenantId . '/oauth2/v2.0/token';
        $params = [
            'form_params' => [
                'client_id' => $this->microsoftClientId,
                'client_secret' => $this->microsoftClientSecret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ],
        ];

        try {
            $response = $client->post($url, $params);
            $data = json_decode($response->getBody(), true);

            if (isset($data['access_token'])) {
                return $data['access_token'];
            } else {
                throw new \RuntimeException('Error fetching access token: ' . json_encode($data));
            }
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Error fetching access token: ' . $e->getMessage());
        }
    }


}
