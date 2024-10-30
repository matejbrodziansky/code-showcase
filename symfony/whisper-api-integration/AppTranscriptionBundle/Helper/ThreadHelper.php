<?php

namespace AppTranscriptionBundle\Helper;


use Doctrine\ORM\EntityManager;
use AppImportBundle\Thread\Type\AbstractType;
use Symfony\Component\HttpFoundation\File\File;
use Imatic\Bundle\CommunicatorBundle\Entity\Thread;
use Imatic\Bundle\CommunicatorBundle\Entity\ThreadType;
use Imatic\Bundle\CommunicatorBundle\Entity\Participant;
use Imatic\Bundle\CommunicatorBundle\Entity\ThreadFileEvent;
use Imatic\Bundle\CommunicatorBundle\Entity\ThreadMessageEvent;
use Imatic\Bundle\DataBundle\Data\Query\QueryExecutorInterface;
use Imatic\Bundle\DataBundle\Data\Command\CommandExecutorInterface;
use Imatic\Bundle\CommunicatorBundle\Data\Query\ThreadType\ThreadTypeByCodeQuery;
use Imatic\Bundle\CommunicatorBundle\Data\Query\Participant\ParticipantByExternalIdQuery;

/**
 * Thread Helper
 *
 * @author Lukas Landa <lukas.landa@imatic.cz>
 */
class ThreadHelper
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var QueryExecutorInterface
     */
    protected $queryExecutor;

    /**
     * @var CommandExecutorInterface
     */
    protected $commandExecutor;

    /**
     * @var AbstractType
     */
    protected $threadType;



    const SUBJECT_PREFIX = 'Telefonní záznamník - ';
    const THREAD_TYPE_CODE = 'phone_voicemail';

    public function __construct(
        EntityManager $em,
        QueryExecutorInterface $qe,
        CommandExecutorInterface $commandExecutor
    ) {
        $this->entityManager = $em;
        $this->queryExecutor = $qe;
        $this->commandExecutor = $commandExecutor;
    }

    /**
     * Set thread type
     *
     * @param AbstractType $threadType
     */
    public function setThreadType(AbstractType $threadType)
    {
        $this->threadType = $threadType;
    }


    /**
     * Create thread
     *
     * @return Thread
     */
    public function createThread(array $transcriptionData): Thread
    {

        $author = $this->getThreadAuthor($transcriptionData['user']);

        $thread = new Thread(
            self::SUBJECT_PREFIX . $author->getName(),
            $this->getThreadType(self::THREAD_TYPE_CODE),
            $author
        );

        $createdAt = new \DateTime($transcriptionData['created_at']);
        $thread
            ->setNote('')
            ->setCreatedAt($createdAt)
            ->getEvents()->first()->setCreatedAt($createdAt)
        ;

        $threadMessageEvent = new ThreadMessageEvent($transcriptionData['whisper_api']['text'], $author, $thread);
        $threadMessageEvent->setCreatedAt($createdAt);
        $thread->addEvent($threadMessageEvent);

        $filePath = $transcriptionData['whisper_api']['file_path'];
        if (file_exists($filePath)) {
            $file = new File($filePath);
            $fileEvent = new ThreadFileEvent($file, $author);
            $fileEvent->setCreatedAt($createdAt);
            $fileEvent->getThreadFile()->setOriginalName(basename($filePath));
            $thread->addEvent($fileEvent);
        } else {
            throw new \Exception("File does not exist: " . $filePath);
        }
        return $thread;
    }




    /**
     * Get participant
     *
     * @param int $id
     * @return Participant
     */
    private function getParticipant($id)
    {
        return $this->queryExecutor->execute(new ParticipantByExternalIdQuery($id));
    }

    /**
     * Get thread type
     *
     * @param string $code
     * @return ThreadType
     */
    private function getThreadType($code)
    {
        return $this->queryExecutor->execute(new ThreadTypeByCodeQuery($code));
    }

    /**
     * Get thread author or create anonymous if not exists
     *
     * @return Participant
     */
    private function getThreadAuthor(array $userData): Participant
    {
        $author = $this->getParticipant($userData['id_users']);

        if(!$author){
            throw new \Exception('Author not found');
        }

        return $author;
    }
}
