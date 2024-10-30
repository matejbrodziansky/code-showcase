<?php

namespace AppTranscriptionBundle\Service;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;

class StudentDataService
{
    private Connection $sisConnection;
    private string $studentListPhoneCallsView;
    private LoggerInterface $logger;

    public function __construct
    (
        Connection      $sisConnection,
        string          $studentListPhoneCallsView,
        LoggerInterface $logger
    )
    {
        $this->sisConnection = $sisConnection;
        $this->studentListPhoneCallsView = $studentListPhoneCallsView;
        $this->logger = $logger;
    }


    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getStudentData(): array
    {
        try {

            $qb = $this->sisConnection->createQueryBuilder();

            $qb->select('*')
                ->from($this->studentListPhoneCallsView);

            $stmt = $qb->execute();

            return $stmt->fetchAllAssociative();

        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch student data: ' . $e->getMessage());
            throw $e;
        }
    }
}