<?php

namespace AppBundle\Entity;

use AppBundle\Component\Helper\MathHelper;
use AppBundle\Enum\CourseType;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity()]
class SeminarDate implements Stringable
{
    use IdTrait;

    #[
        ORM\Column(
            type: 'integer',
            length: 6,
            nullable: true,
        ),
        Assert\Length(
            min: 1,
            max: 6
        ),
    ]
    private ?int $webMeetingId = null;

    #[
        ORM\Column(
            type: 'time',
            nullable: true,
        ),
    ]
    private ?\DateTime $webMeetingStart = null;

    #[
        ORM\Column(
            type: 'time',
            nullable: true,
        ),
    ]
    private ?\DateTime $webMeetingEnd = null;

    public function getWebMeetingId(): ?int
    {
        return $this->webMeetingId;
    }

    public function setWebMeetingId(?int $webMeetingId): void
    {
        $this->webMeetingId = $webMeetingId;
    }

    public function getWebMeetingStart(): ?DateTime
    {
        return $this->webMeetingStart;
    }

    public function setWebMeetingStart(?DateTime $webMeetingStart): void
    {
        $this->webMeetingStart = $webMeetingStart;
    }

    public function getWebMeetingEnd(): ?DateTime
    {
        return $this->webMeetingEnd;
    }

    public function setWebMeetingEnd(?DateTime $webMeetingEnd): void
    {
        $this->webMeetingEnd = $webMeetingEnd;
    }

    public function createWebMeetingUrl(): ?string
    {
        if ($this->webMeetingId === null) {
            return null;
        }

        return 'https://admin.webmeeting.cz/meeting/detail?meetingId=' . $this->webMeetingId;
    }

    public function canSendWebMeetingNotification(): bool
    {
        return $this->getAllowOnlineParticipation();
    }
}
