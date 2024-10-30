<?php
namespace App\Command;

use App\Service\GraphService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'microsoft:list-groups',
    description: 'List all groups from Microsoft Graph API'
)]
class MicrosoftListGroupsCommand extends Command
{
    public function __construct
    (
        private GraphService $graphService,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $groups = $this->graphService->getAllGroups();

        foreach ($groups as $group) {

            $io->section($group->getDisplayName());
            $io->text([
                '<info>Id:</info> ' . $group->getId(),
            ]);
        }

        return Command::SUCCESS;
    }
}