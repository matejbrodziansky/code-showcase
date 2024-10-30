<?php

namespace App\Command;

use App\Data\Helper\GraphHelper;
use App\Service\GraphService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'microsoft:list-groups-members',
    description: 'List all groups members from Microsoft Graph API'
)]
class MicrosoftListGroupsMembers extends Command
{
    public function __construct
    (
        private GraphService $graphService,
        private GraphHelper  $graphHelper
    )
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $groups = $this->graphService->getAllGroups();

        $groupsWithMembers = $this->graphService->getAllGroupsMembers($groups);

        foreach ($groupsWithMembers as $group) {

            $groupMembers = $this->graphHelper->extractGroupMembersNames($group->members);

            $io->section($group->getDisplayName());
            $io->text([
                '<info>Id:</info> ' . $group->getId(),
                '<info>Count of members:</info> ' . count($groupMembers),
                '<info>Members:</info> ' . implode(', ', $groupMembers),
            ]);
        }

        return Command::SUCCESS;
    }
}