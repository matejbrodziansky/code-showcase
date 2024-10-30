<?php declare(strict_types=1);

namespace AppTranscriptionBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppTranscriptionBundle\Service\TranscriptionService;

class TranscriptionCommand extends Command
{

    protected static $defaultName = 'transcription:wav:files';

    private TranscriptionService $transcriptionService;
    private LoggerInterface $logger;

    public function __construct(

        TranscriptionService $transcriptionService,
        LoggerInterface      $logger
    )
    {
        parent::__construct();
        $this->transcriptionService = $transcriptionService;
        $this->logger = $logger;

    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Transcribe wav files into the threads.')
            ->setHelp('...');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->transcriptionService->transcribe();

        if ($result) {
            $message = 'Transcription completed successfully.';
            $this->logger->info($message);
            $io->success($message);
        } else {
            $message = 'Transcription found no files to process.';
            $this->logger->info($message);
            $io->success($message);
        }
    }
}

