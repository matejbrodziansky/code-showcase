<?php

namespace App\Command;

use App\Service\GraphService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'microsoft:clear-group-by-id',
    description: 'Clear group by id'
)]
class MicrosoftClearGroupByIdCommand extends Command
{
    public function __construct
    (
        private GraphService $graphService,
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('groupId', InputArgument::REQUIRED, 'The group id');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $groupId = $input->getArgument('groupId');

        $result = $this->graphService->clearGroupById($groupId);

        if (true !== $result) {
            $io->error('Error clearing group');
            return Command::FAILURE;

        }

        $io->success('Group successfully cleared');

        return Command::SUCCESS;

    }
}