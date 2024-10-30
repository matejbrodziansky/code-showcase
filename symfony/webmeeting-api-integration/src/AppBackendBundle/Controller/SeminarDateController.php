<?php

namespace AppBackendBundle\Controller;

use AppBundle\Data\Filter\SeminarDate\SeminarDateFilter;
use AppBundle\Data\Handler\SeminarDate\GenerateAttendanceDocumentHandler;
use AppBundle\Data\Handler\SeminarDate\GenerateCertificateHandler;
use AppBundle\Data\Handler\SeminarDate\GenerateDocumentHandler;
use AppBundle\Data\Handler\SeminarDate\GenerateSheetHandler;
use AppBundle\Data\Handler\SeminarDate\MoveParticipantsHandler;
use AppBundle\Data\Handler\SeminarDate\SendCertificatesHandler;
use AppBundle\Data\Handler\SeminarDate\SendNotificationsHandler;
use AppBundle\Data\Handler\SeminarDate\WebMeetingNotificationSendHandler;
use AppBundle\Data\Query\SeminarDate\OrderableIndefiniteSeminarDateListQuery;
use AppBundle\Entity\SeminarDate;
use AppBundle\Form\Type\SeminarDateMoveParticipantsType;
use AppBundle\Form\Type\SeminarDateType;
use AppBundle\Form\Type\SeminarParticipantEmailChoiceType;
use AppBundle\Form\Type\SeminarParticipantNotificationEmailChoiceType;
use AppBundle\Form\Type\WebMeetingNotificationType;
use AppBundle\Service\QueryExecutor;
use Imatic\Bundle\ControllerBundle\Controller\Api\Command\CommandApi;
use Imatic\Bundle\ControllerBundle\Controller\Api\Form\FormApi;
use Imatic\Bundle\ControllerBundle\Controller\Api\Listing\ListingApi;
use Imatic\Bundle\ControllerBundle\Controller\Api\Show\ShowApi;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\Command\CreateHandler;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\Command\DeleteHandler;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\Command\EditHandler;
use Symfony\Component\HttpFoundation\Request;
use Imatic\Bundle\ControllerBundle\Controller\Api\ApiTrait;
use Imatic\Bundle\DataBundle\Data\Command\CommandResultInterface;
use AppBundle\Entity\Seminar;
use AppBundle\Entity\SeminarParticipant;
use AppBundle\Data\Query\SeminarDate\SeminarDateQuery;
use AppBundle\Data\Query\SeminarDate\SeminarDateListQuery;
use AppBundle\Data\Query\SeminarParticipant\SeminarParticipantQuery;
use AppBundle\Data\Query\SeminarParticipant\SeminarParticipantListQuery;
use AppBundle\Data\Query\SeminarParticipant\SeminarParticipantCountQuery;
use AppBundle\Data\Query\SeminarDate\FutureSeminarDateListQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/seminars/{seminarId}/dates')]
class SeminarDateController extends AbstractController
{
    use ApiTrait;



    #[Route('/{seminarDateId}/send-webmeeting-notifications')]
    public function sendWebMeetingNotificationsAction(Seminar $seminar, int $seminarDateId, QueryExecutor $queryExecutor): Response
    {
        $seminarDate = $queryExecutor->execute(new SeminarDateQuery($seminarDateId, $seminar));
        $participants = $queryExecutor->execute(new SeminarParticipantListQuery($seminarDate));

        if (!$seminarDate->getAllowOnlineParticipation()){
            throw new NotFoundHttpException('This action is only available for webMeeting dates.');
        }

        /** @var FormApi $form */
        $form = $this
            ->form(WebMeetingNotificationType::class, null, [
                'region' => $seminar->getRegion(),
                'participants' => SeminarParticipant::filterWebMeetingNotificationRecipients($participants),
            ]);
        return $form
            ->command(WebMeetingNotificationSendHandler::class, [
                'seminar_date' => $seminarDate,
            ])
            ->successRedirect('appbackend_seminardate_list', ['seminarId' => $seminar->getId()])
            ->setTemplateName('@AppBackend/SeminarDate/send_webmeeting_notifications.html.twig')
            ->addTemplateVariable('seminar_date', $seminarDate)
            ->getResponse();
    }

    #[Route('/{id}/move-participants')]
    public function moveParticipantsAction(Seminar $seminar, int $id, QueryExecutor $queryExecutor): Response
    {
        $date = $queryExecutor->execute(new SeminarDateQuery($id, $seminar));
        $participants = $queryExecutor->execute(new SeminarParticipantListQuery($date));
        $seminarDates = $queryExecutor->execute(new FutureSeminarDateListQuery(null, $seminar->getRegion()));
        $indefiniteDates = $queryExecutor->execute(new OrderableIndefiniteSeminarDateListQuery($seminar));

        /** @var FormApi $form */
        $form = $this
            ->form(SeminarDateMoveParticipantsType::class, null, [
                'participants' => SeminarParticipant::filterRelocationCandidates($participants),
                'dates' => array_merge($seminarDates, $indefiniteDates),
            ]);
        return $form
            ->command(MoveParticipantsHandler::class)
            ->successRedirect('appbackend_seminardate_list', ['seminarId' => $seminar->getId()])
            ->setTemplateName('@AppBackend/SeminarDate/move_participants.html.twig')
            ->addTemplateVariable('item', $date)
            ->getResponse()
        ;
    }

}
