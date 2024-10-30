<?php

namespace AppBackendBundle\Controller;

use AppBundle\Data\Handler\SeminarDate\MoveParticipantsHandler;
use AppBundle\Data\Handler\SeminarDate\SendCertificatesHandler;
use AppBundle\Data\Handler\SeminarDate\SendNotificationsHandler;
use AppBundle\Data\Handler\SeminarDate\WebMeetingNotificationSendHandler;
use AppBundle\Entity\OrderSeminarDate;
use AppBundle\Enum\CourseType;
use AppBundle\Form\Type\OrderSeminarDateType;
use AppBundle\Form\Type\SeminarDateMoveParticipantsType;
use AppBundle\Form\Type\SeminarParticipantEmailChoiceType;
use AppBundle\Form\Type\SeminarParticipantNotificationEmailChoiceType;
use AppBundle\Form\Type\WebMeetingNotificationType;
use AppBundle\Service\QueryExecutor;
use Exception;
use Imatic\Bundle\ControllerBundle\Controller\Api\Command\CommandApi;
use Imatic\Bundle\ControllerBundle\Controller\Api\Form\FormApi;
use Imatic\Bundle\ControllerBundle\Controller\Api\Listing\ListingApi;
use Imatic\Bundle\ControllerBundle\Controller\Api\Show\ShowApi;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\Command\DeleteHandler;
use Imatic\Bundle\DataBundle\Data\Driver\DoctrineORM\Command\EditHandler;
use Imatic\Bundle\ControllerBundle\Controller\Api\ApiTrait;
use AppBundle\Entity\Order;
use AppBundle\Entity\SeminarParticipant;
use AppBundle\Data\Query\OrderSeminarDate\OrderSeminarDateQuery;
use AppBundle\Data\Query\OrderSeminarDate\OrderSeminarDateListQuery;
use AppBundle\Data\Query\SeminarDate\FutureSeminarDateListQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders/{orderId}/dates')]
class OrderSeminarDateController extends AbstractController
{
    use ApiTrait;

    #[Route('')]
    public function listAction(Order $order): Response
    {
        /** @var ListingApi $listing */
        $listing = $this
            ->listing(new OrderSeminarDateListQuery($order, true));
        return $listing
            ->enablePersistentDisplayCriteria()
            ->setTemplateName('@AppBackend/OrderSeminarDate/list.html.twig')
            ->addTemplateVariable('order', $order)
            ->getResponse()
        ;
    }

    #[Route('/{id}/edit')]
    public function editAction(Order $order, int $id): Response
    {
        /** @var FormApi $form */
        $form = $this
            ->form(OrderSeminarDateType::class);
        return $form
            ->edit(new OrderSeminarDateQuery($id, $order))
            ->setTemplateName('@AppBackend/OrderSeminarDate/edit.html.twig')
            ->addTemplateVariable('order', $order)
            ->command(EditHandler::class, ['class' => OrderSeminarDate::class, 'id' => $id])
            ->successRedirect('appbackend_orderseminardate_show', ['id' => $id, 'orderId' => $order->getId()])
            ->getResponse()
        ;
    }

    /**
     * @throws Exception
     */
    #[Route('/{id}/delete')]
    public function deleteAction(Order $order, $id): Response
    {
        /** @var CommandApi $command */
        $command = $this
            ->command(DeleteHandler::class, [
                'class' => OrderSeminarDate::class,
                'query_object' => new OrderSeminarDateQuery($id, $order),
            ]);
        return $command
            ->redirect('appbackend_orderseminardate_list', ['orderId' => $order->getId()])
            ->getResponse()
        ;
    }

    #[Route('/{id}')]
    public function showAction(Order $order, $id): Response
    {
        /** @var ShowApi $show */
        $show = $this
            ->show(new OrderSeminarDateQuery($id, $order));
        return $show
            ->setTemplateName('@AppBackend/OrderSeminarDate/show.html.twig')
            ->addTemplateVariable('order', $order)
            ->getResponse()
        ;
    }

    #[Route('/{id}/send-certificates')]
    public function sendCertificatesAction(Order $order, int $id, QueryExecutor $queryExecutor): Response
    {
        $orderDate = $queryExecutor->execute(new OrderSeminarDateQuery($id, $order));

        /** @var FormApi $form */
        $form = $this
            ->form(SeminarParticipantEmailChoiceType::class, [
                'participants' => $orderDate->getCertificateRecipients(true),
            ], [
                'participants' => $orderDate->getCertificateRecipients(),
                'submit_label' => 'Send certificates',
                'help' => 'Send certificates help',
            ]);
        return $form
            ->command(SendCertificatesHandler::class, [
                'date' => $orderDate->getDate(),
            ])
            ->successRedirect('appbackend_orderseminardate_list', ['orderId' => $order->getId()])
            ->setTemplateName('@AppBackend/SeminarDate/send_certificates.html.twig')
            ->addTemplateVariable('item', $orderDate->getDate())
            ->getResponse()
        ;
    }

    #[Route('/{id}/send-notifications')]
    public function sendNotificationsAction(Order $order, int $id, QueryExecutor $queryExecutor): Response
    {
        $orderDate = $queryExecutor->execute(new OrderSeminarDateQuery($id, $order));

        /** @var FormApi $form */
        $form = $this
            ->form(SeminarParticipantNotificationEmailChoiceType::class, null, [
                'region' => $order->getRegion(),
                'participants' => $orderDate->getNotificationRecipients(),
                'help' => 'Send notifications help',
            ]);
        return $form
            ->command(SendNotificationsHandler::class, [
                'date' => $orderDate->getDate(),
            ])
            ->successRedirect('appbackend_orderseminardate_list', ['orderId' => $order->getId()])
            ->setTemplateName('@AppBackend/SeminarDate/send_notifications.html.twig')
            ->addTemplateVariable('item', $orderDate->getDate())
            ->getResponse();
    }

    #[Route('/{orderSeminarDateId}/send-webmeeting-notifications')]
    public function sendWebMeetingNotificationsAction(Order $order, int $orderSeminarDateId, QueryExecutor $queryExecutor): Response
    {
        $orderSeminarDate = $queryExecutor->execute(new OrderSeminarDateQuery($orderSeminarDateId, $order));

        if ($orderSeminarDate && $orderSeminarDate->getCourseType() !== CourseType::ONLINE_PARTICIPATION) {
            throw new NotFoundHttpException('This action is only available for webMeeting dates.');
        }
        /** @var FormApi $form */
        $form = $this
            ->form(WebMeetingNotificationType::class, null, [
                'region' => $order->getRegion(),
                'participants' => $orderSeminarDate->getWebMeetingNotificationRecipients(),
            ]);
        return $form
            ->command(WebMeetingNotificationSendHandler::class, [
                'seminar_date' => $orderSeminarDate->getDate(),
            ])
            ->successRedirect('appbackend_orderseminardate_list', ['orderId' => $order->getId()])
            ->setTemplateName('@AppBackend/SeminarDate/send_webmeeting_notifications.html.twig')
            ->addTemplateVariable('seminar_date', $orderSeminarDate->getDate())
            ->getResponse();
    }

    #[Route('/{id}/move-participants')]
    public function moveParticipantsAction(Order $order, int $id, QueryExecutor $queryExecutor): Response
    {
        $orderDate = $queryExecutor->execute(new OrderSeminarDateQuery($id, $order));
        $seminarDates = $queryExecutor->execute(new FutureSeminarDateListQuery(null, $order->getRegion()));

        /** @var FormApi $form */
        $form = $this
            ->form(SeminarDateMoveParticipantsType::class, null, [
                'participants' => SeminarParticipant::filterRelocationCandidates($orderDate->getParticipants()),
                'dates' => $seminarDates,
            ]);
        return $form
            ->command(MoveParticipantsHandler::class)
            ->successRedirect('appbackend_orderseminardate_list', ['orderId' => $order->getId()])
            ->setTemplateName('@AppBackend/SeminarDate/move_participants.html.twig')
            ->addTemplateVariable('item', $orderDate->getDate())
            ->getResponse();
    }
}
