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
    name: 'microsoft:add-user-to-group',
    description: 'Add user to group by email and group name'
)]
class MicrosoftAddUserToGroupCommand extends Command
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
        $this->addArgument('userEmail', InputArgument::REQUIRED, 'The user id');
        $this->addArgument('groupName', InputArgument::REQUIRED, 'The group id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userEmail = $input->getArgument('userEmail');
        $groupName = $input->getArgument('groupName');

        $result = $this->graphService->addUserToGroup($userEmail, $groupName);

        if (true !== $result) {
            $io->error('User or group not found. Or user is already in group');
            return Command::FAILURE;

        }

        $io->success('User successfully added to group');

        return Command::SUCCESS;

    }
}