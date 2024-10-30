<?php

namespace AppBundle\Component\Helper;

use Exception;

class WebMeetingHelper
{
    public function transformParticipants(array $participants): array
    {
        $processedParticipants = [];

        foreach ($participants as $participant) {

            $firstName = $participant->getFirstName() ?? $participant->getName();
            $lastName = $participant->getLastName() ?? '';

            $processedParticipant = [Comp
                'number' => $participant->getId(),
                'surname' => $lastName,
                'firstname' => $firstName,
                'email' => $participant->getEmail()
            ];

            $processedParticipants[] = $processedParticipant;
        }
        return $processedParticipants;
    }

    public function assignWebMeetingData(array $participants, array $participantsUrls, int $webMeetingId): array
    {
        $participantsUrls = array_values($participantsUrls);

        foreach ($participants as $key => $participant) {

            parse_str(parse_url($participantsUrls[$key])['query'], $urlToken);

            $participant->setWebMeetingUrlToken($urlToken['token']);
            $participant->setWebMeetingId($webMeetingId);
        }

        return $participants;
    }

    public function concatenateSpeakerNames(array $speakers): string
    {
        $speakerNames = [];

        foreach ($speakers as $speaker) {
            $speakerNames[] = $speaker->getName();
        }
        return implode(', ', $speakerNames);
    }
}