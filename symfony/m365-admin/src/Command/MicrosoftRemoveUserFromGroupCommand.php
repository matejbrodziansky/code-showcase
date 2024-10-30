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
    name: 'microsoft:remove-user-from-group',
    description: 'Remove user From group by email and group name'
)]
class MicrosoftRemoveUserFromGroupCommand extends Command
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
        $this->addArgument('userEmail', InputArgument::REQUIRED, 'The user email');
        $this->addArgument('groupName', InputArgument::REQUIRED, 'The group name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userEmail = $input->getArgument('userEmail');
        $groupName = $input->getArgument('groupName');


        $result = $this->graphService->removeUserFromGroup($userEmail, $groupName);

        if (true !== $result) {
            $io->error('User or group not found. Or user is already removed');
            return Command::FAILURE;
        }

        $io->success('User successfully removed from group');

        return Command::SUCCESS;

    }
}