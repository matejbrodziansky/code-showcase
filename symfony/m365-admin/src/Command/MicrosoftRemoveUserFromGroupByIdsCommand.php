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
    name: 'microsoft:remove-user-from-group-by-ids',
    description: 'Remove user From group by ids'
)]
class MicrosoftRemoveUserFromGroupByIdsCommand extends Command
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

        $result = $this->graphService->removeUserFromGroupByIds($userId, $groupId);

        if (true !== $result) {
            $io->error('User or group not found. Or user is already removed');
            return Command::FAILURE;

        }

        $io->success('User successfully removed from group');

        return Command::SUCCESS;

    }
}