<?php

namespace AppBundle\Service\WebMeeting;

use AppBundle\Component\Helper\WebMeetingHelper;
use AppBundle\Entity\SeminarDate;
use AppBundle\Service\WebMeeting\Api\WebMeetingApiClient;
use Psr\Log\LoggerInterface;

class WebMeeting
{
    private const WM_API_URL = 'https://admin.webmeeting.cz/api';
    private const HTML5_CLIENT = true;
    // Stálá místnost pro 51 (až 51 účastníků kontinuálně na 1 rok)
    public const WEB_MEETING_TYPE = 5;

    public function __construct(
        private readonly string  $webMeetingApiClient,
        private readonly string  $webMeetingApiRequestSecret,
        private readonly string  $webMeetingResponseSecret,
        private WebMeetingHelper $webMeetingHelper,
        private LoggerInterface  $logger
    )
    {
    }

    private function createApiClient(): WebMeetingApiClient
    {
        return new WebmeetingApiClient(
            $this->webMeetingApiClient,
            $this->webMeetingApiRequestSecret,
            $this->webMeetingResponseSecret,
            self::WM_API_URL
        );
    }

    public function createWebMeeting(SeminarDate $date)
    {
        $client = $this->createApiClient();

        $seminarName = $date->getSeminar()->getName();
        $seminarDescription = $date->getSeminar()->getDescription();
        $speakerNames = $this->webMeetingHelper->concatenateSpeakerNames($date->getSeminar()->getLecturers());
        $webMeetingStartTime = $date->getWebMeetingStart()->format('H:i');
        $webMeetingStartDate = $date->getStartDate()->format('d.m.Y');
        $webMeetingStart = $webMeetingStartDate . ' ' . $webMeetingStartTime;

        try {
            $meetingId = $client->createMeeting($this->webMeetingApiClient, $seminarName, $webMeetingStart, $speakerNames, $seminarDescription, self::WEB_MEETING_TYPE);
            $client->configureMeeting($this->webMeetingApiClient, $meetingId, ['auto_start_recording' => 0]);

            return $meetingId;
        } catch (\Throwable $e) {
            $this->logger->error('Failed to create web meeting: ' . $e->getMessage());
            throw new \Exception('Failed to create web meeting: ' . $e->getMessage());
        }
    }

    public function importParticipantAndGetEnterURL(int $webMeetingId, array $participants): array
    {
        $processedParticipants = $this->webMeetingHelper->transformParticipants($participants);

        $client = $this->createApiClient();

        try {
            $participantsUrls = $client->importParticipantAndGetEnterURL($this->webMeetingApiClient, $webMeetingId, $processedParticipants, $client::ACCESS_ONLINE_AND_RECORD, self::HTML5_CLIENT);

            return $this->webMeetingHelper->assignWebMeetingData($participants, $participantsUrls, $webMeetingId);

        } catch (\Throwable $e) {
            $this->logger->error('Failed to import participants and get enter URL: ' . $e->getMessage());
            throw new \Exception('Failed to import participants and get enter URL: ' . $e->getMessage());
        }
    }
}