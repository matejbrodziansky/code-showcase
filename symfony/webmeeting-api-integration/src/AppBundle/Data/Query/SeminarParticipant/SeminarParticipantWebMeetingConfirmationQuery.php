<?php

namespace AppBundle\Data\Query\SeminarParticipant;

use Doctrine\ORM\QueryBuilder;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\QueryObjectInterface;
use Imatic\Bundle\DataBundle\Data\Query\SingleResultQueryObjectInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Region;

class SeminarParticipantWebMeetingConfirmationQuery implements QueryObjectInterface, SingleResultQueryObjectInterface
{
    public function __construct(private int $seminarParticipantId, private string $webMeetingConfirmationCode, private Region $region)
    {
    }

    public function build(EntityManager $em): QueryBuilder
    {
        return $em
            ->createQueryBuilder()
            ->select('p', 'od', 'd', 's')
            ->from('AppBundle:SeminarParticipant', 'p')
            ->join('p.date', 'od')
            ->join('od.date', 'd')
            ->join('d.seminar', 's')
            ->where('p = :seminarParticipantId AND p.webMeetingConfirmationCode = :webMeetingConfirmationCode AND s.region = :region AND d.endDate >= CURRENT_DATE()')
            ->setParameters([
                'seminarParticipantId' => $this->seminarParticipantId,
                'webMeetingConfirmationCode' => $this->webMeetingConfirmationCode,
                'region' => $this->region,
            ])
        ;
    }
}
