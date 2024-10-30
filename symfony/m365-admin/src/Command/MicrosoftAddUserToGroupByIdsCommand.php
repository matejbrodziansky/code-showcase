<?php

namespace App\Command;

use App\Data\Helper\GraphHelper;
use App\Service\GraphService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'microsoft:add-user-to-group-by-ids',
    description: 'List all users from Microsoft Graph API'
)]
class MicrosoftAddUserToGroupByIdsCommand extends Command
{
    public function __construct
    (
        private GraphService $graphService,
        private GraphHelper  $graphHelper
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('userId', InputArgument::REQUIRED, 'The user id');
        $this->addArgument('groupId', InputArgument::REQUIRED, 'The group id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userId = $input->getArgument('userId');
        $groupId = $input->getArgument('groupId');

        $result = $this->graphService->addUserToGroupByIds($userId, $groupId);

        if (true !== $result) {
            $io->error('User or group not found. Or user is already in group');
            return Command::FAILURE;

        }

        $io->success('User successfully added to group');

        return Command::SUCCESS;
    }
}