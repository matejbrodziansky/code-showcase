<?php declare(strict_types=1);

namespace AppTranscriptionBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppTranscriptionBundle\Service\FileService;

class CleanUpFilesCommand extends Command
{
    protected static $defaultName = 'transcription:clean-up:wav:files';

    private FileService $fileService;
    private LoggerInterface $logger;

    private int $cleanUpIntervalDays;

    public function __construct(

        FileService $fileService,
        LoggerInterface      $logger,
        int $cleanUpIntervalDays
    )
    {
        parent::__construct();
        $this->fileService = $fileService;
        $this->logger = $logger;
        $this->cleanUpIntervalDays = $cleanUpIntervalDays;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Clean up transcribed wav files .')
            ->setHelp('...');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $result =  $this->fileService->cleanUpOldFiles($this->cleanUpIntervalDays);

        $io = new SymfonyStyle($input, $output);

        if ($result) {
            $message = 'Clean up completed successfully.';
            $this->logger->info($message);
            $io->success($message);
        } else {
            $message = 'Clean up found no files to process.';
            $this->logger->info($message);
            $io->warning($message);
        }
    }
}

