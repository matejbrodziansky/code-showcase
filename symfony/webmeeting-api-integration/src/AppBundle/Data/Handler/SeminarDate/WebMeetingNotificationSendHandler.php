<?php

namespace AppBundle\Data\Handler\SeminarDate;

use AppBundle\Mail\Builder\SeminarDateWebMeetingNotificationBuilder;
use AppBundle\Service\AppFormatter;
use AppBundle\Service\WebMeeting\WebMeeting;
use DateTime;
use Imatic\Bundle\DataBundle\Data\Command\CommandInterface;
use AppBundle\Data\Handler\Handler;
use Imatic\Bundle\DataBundle\Data\Command\CommandResultInterface;
use Symfony\Component\Mailer\MailerInterface;

class WebMeetingNotificationSendHandler extends Handler
{
    public function __construct(
        private MailerInterface                          $mailer,
        private SeminarDateWebMeetingNotificationBuilder $seminarDateWebMeetingNotificationBuilder,
        private WebMeeting                               $webMeeting,
        private AppFormatter                             $appFormatter,
    )
    {
    }

    protected function process(CommandInterface $command): ?CommandResultInterface
    {
        $formData = $command->getParameter('data');
        $seminarDate = $command->getParameter('seminar_date');

        $webMeetingId = $seminarDate->getWebMeetingId();

        if ($webMeetingId === null) {
            $webMeetingId = $this->webMeeting->createWebMeeting($seminarDate);
            $seminarDate->setWebMeetingId($webMeetingId);
        }

        $formData['participants'] = $this->webMeeting->importParticipantAndGetEnterURL($webMeetingId, $formData['participants']);

        foreach ($formData['participants'] as $participant) {

            if (!$participant->getWebMeetingConfirmationCode()){
                $participant->generateWebMeetingConfirmationCode();
            }

            $this->mailer->send($this->seminarDateWebMeetingNotificationBuilder->generate([
                'date' => $seminarDate,
                'participant' => $participant,
                'attachment' => $formData['attachment'] ?? null,
                'documentation_attachment' => $formData['documentation_attachment'],
                'web_meeting_url' => $participant->createWebMeetingUrl(),
                'communication_option' => $formData['communication_option'],
                'client_service_director_signature' => $formData['client_service_director_signature'],
                'day' => strtolower($this->appFormatter->formatDateToWord($seminarDate->getStartDate(), ['short' => false])),
            ]));

            $participant->setWebMeetingNotifiedAt(new DateTime());
            $this->em->flush();
        }
        return null;
    }
}
