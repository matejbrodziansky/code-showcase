<?php

namespace App\Controller;

use App\Service\GraphService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MicrosoftGroupsManagementController extends AbstractController
{

    public function __construct(
        private GraphService          $graphService,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
    }


    #[Route('/teams-groups-management/users', name: 'app_teams_groups_management_users')]
    public function listUsers(): Response
    {

        $users = $this->graphService->getAllUsersWithGroups();
        $groups = $this->graphService->getAllGroups();

        return $this->render('microsoft_groups_management/index.html.twig', [
            'users' => $users,
            'groups' => $groups,
        ]);
    }

    #[Route('/teams-groups-management/users/{userId}/add-to-group/{groupId}', name: 'app_teams_groups_management_users_add_to_group')]
    public function addUserToGroup(string $userId, string $groupId): RedirectResponse
    {

        $this->graphService->addUserToGroupByIds($userId, $groupId);

        $message = "User successfully added to group";
        $this->addFlash('success', $message);

        return new RedirectResponse($this->urlGenerator->generate('app_teams_groups_management_users'));
    }

    #[Route('/teams-groups-management/users/{userId}/delete-from-group/{groupId}', name: 'app_teams_groups_management_users_delete_from_group')]
    public function removeUserFromGroup(string $userId, string $groupId): RedirectResponse
    {

        $this->graphService->removeUserFromGroupByIds($userId, $groupId);

        $message = "User successfully deleted from group";
        $this->addFlash('success', $message);

        return new RedirectResponse($this->urlGenerator->generate('app_teams_groups_management_users'));
    }

    #[Route('/teams-groups-management/groups', name: 'app_teams_groups_management_groups')]
    public function listGroups(): Response
    {

        $groups = $this->graphService->getAllGroups();
        $groups = $this->graphService->getAllGroupsMembers($groups);

        return $this->render('microsoft_groups_management/groups/index.html.twig', [
            'groups' => $groups,
        ]);
    }

    #[Route('/teams-groups-management/groups/clear-group/{groupId}', name: 'app_teams_groups_management_groups_clear_group')]
    public function clearGroup(string $groupId): RedirectResponse
    {
        $this->graphService->clearGroupById($groupId);

        $message = "Group successfully cleared";
        $this->addFlash('success', $message);

        return new RedirectResponse($this->urlGenerator->generate('app_teams_groups_management_groups'));
    }
}
