<?php

namespace AppFrontendBundle\Controller;

use AppBundle\Data\Query\SeminarParticipant\SeminarParticipantWebMeetingConfirmationQuery;
use AppBundle\Entity\SeminarParticipant;
use AppBundle\Service\QueryExecutor;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Imatic\Bundle\ControllerBundle\Controller\Api\ApiTrait;
use AppBundle\Entity\Region;
use AppBundle\Data\Query\SeminarParticipant\SeminarParticipantConfirmationQuery;
use Symfony\Component\Routing\Annotation\Route;

class SeminarParticipantController extends AbstractController
{
    use ApiTrait;


    #[
        Route('/seminar-participant-confirmation-web-meeting/{seminarParticipantId}/{webMeetingConfirmationCode}'),
        Template
    ]
    public function confirmationWebMeetingAction(
        Region $region,
        int $seminarParticipantId,
        string $webMeetingConfirmationCode,
        EntityManagerInterface $entityManager,
        QueryExecutor $queryExecutor
    ): array
    {
        $participant = $queryExecutor->execute(new SeminarParticipantWebMeetingConfirmationQuery($seminarParticipantId, $webMeetingConfirmationCode, $region), false);

        if ($participant instanceof SeminarParticipant) {
            if ($participant->isWebMeetingConfirmed()) {
                // already confirmed
                $result = 'web-meeting-already_confirmed';
                $resultType = 'warning';
            } else {
                // confirm
                $participant->setWebMeetingConfirmedAt(new DateTime('NOW'));
                $entityManager->flush();

                $result = 'web-meeting-ok';
                $resultType = 'success';
            }
        } else {
            // not found
            $result = 'not_found';
            $resultType = 'danger';
        }
        return [
            'participant' => $participant,
            'result' => $result,
            'result_type' => $resultType,
        ];
    }
}
