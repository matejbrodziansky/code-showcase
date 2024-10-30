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
    name: 'microsoft:list-users',
    description: 'List all users from Microsoft Graph API'
)]
class MicrosoftListUsersCommand extends Command
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

        $users = $this->graphService->getAllUsersWithGroups();

        foreach ($users as $user) {

            $user->groupsNamesWitchIds = $this->graphHelper->extractGroupNamesWithIds($user->groups);

            $io->section($user->getDisplayName());
            $io->text([
                '<info>Id:</info> ' . $user->getId(),
                '<info>User Principal Name:</info> ' . $user->getUserPrincipalName(),
                '<info>Groups:</info> ' . implode(', ', $user->groupsNamesWitchIds),
            ]);
        }

        return Command::SUCCESS;
    }
}