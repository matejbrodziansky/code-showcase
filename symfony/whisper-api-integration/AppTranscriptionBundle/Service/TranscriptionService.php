<?php

namespace AppTranscriptionBundle\Service;

use AppTranscriptionBundle\Helper\ThreadHelper;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TranscriptionService
{
    private FileService $fileService;
    private WhisperApiService $whisperApiService;
    private EntityManagerInterface $entityManager;
    private ThreadHelper $threadHelper;
    private SisService $sisService;
    private LoggerInterface $logger;
    private string $anonymouseUserId;

    public function __construct(
        FileService            $fileService,
        WhisperApiService      $whisperApiService,
        EntityManagerInterface $entityManager,
        ThreadHelper           $threadHelper,
        SisService             $sisService,
        LoggerInterface        $logger,
        string                 $anonymousUserId
    )
    {
        $this->fileService = $fileService;
        $this->whisperApiService = $whisperApiService;
        $this->entityManager = $entityManager;
        $this->threadHelper = $threadHelper;
        $this->sisService = $sisService;
        $this->logger = $logger;
        $this->anonymousUserId = $anonymousUserId;
    }

    /**
     * @throws Exception
     */
    public function transcribe(): bool
    {
        try {
            $wavFiles = $this->fileService->getWavFiles();

            if (empty($wavFiles)) {
                return false;
            }

            foreach ($wavFiles as $file) {
                $filePath = $this->fileService->getFilePath($file);

                $transcriptionData['caller_number'] = $this->fileService->getCallerPhoneFromMetadata($file);
                $transcriptionData['created_at'] = $this->fileService->getCreatedAtFromMetadata($file);
                $transcriptionData['user'] = $this->sisService->getUserByPhoneNumber($transcriptionData['caller_number']);

                if (empty($transcriptionData['user'])) {
                    if (!$this->anonymousUserId){
                        $this->logger->info('TranscriptionService: User not found', ['caller_number' => $transcriptionData['caller_number']]);
                        continue;
                    }
                    $transcriptionData['user']['id_users'] = $this->anonymousUserId;
                }

                $transcriptionData['whisper_api'] = $this->whisperApiService->transcribe($filePath);

                if ($transcriptionData['whisper_api']['text'] === null) {
                    $this->logger->error('Whisper API did not return any text', ['file_path' => $filePath]);
                    continue;
                }

                $thread = $this->threadHelper->createThread($transcriptionData);

                $this->fileService->moveFileToDone($filePath);

                $this->entityManager->persist($thread);

                $this->entityManager->flush();
            }
            return true;
        } catch (\Exception $e) {
            $message = 'TranscriptionService: Error during transcription: '. $e->getMessage();
            $this->logger->error($message);
            throw new \Exception($message);

        }
    }
}
