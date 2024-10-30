<?php

namespace AppBundle\Entity;

use AppBundle\Enum\CourseType;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
class SeminarParticipant implements PersonInflectionInterface, Stringable
{
    use IdTrait;
    use PersonInflectionTrait;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
    )]
    private ?DateTime $webMeetingNotifiedAt = null;

    #[ORM\Column(
        type: 'string',
        length: 64,
        nullable: true,
    )]
    private ?string $webMeetingUrlToken = null;

    #[
        ORM\Column(
            type: 'integer',
            length: 32,
            nullable: true,
        ),
        Assert\Length(
            min: 1,
            max: 6
        ),
    ]
    private ?int $webMeetingId = null;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
    )]
    private ?DateTime $webMeetingConfirmedAt = null;

    #[ORM\Column(
        type: 'string',
        length: 32,
    )]
    private string $webMeetingConfirmationCode;

    public function __construct()
    {

        $this->generateWebMeetingConfirmationCode();
    }
    /**
     * @param SeminarParticipant[] $participants
     * @return SeminarParticipant[]
     */
    public static function filterWebMeetingNotificationRecipients(array $participants, bool $skipNotified = false): array
    {
        $result = [];

        foreach ($participants as $participant) {
            if ($participant->isAttending() && (!$skipNotified || !$participant->isWebMeetingNotified())) {
                $result[] = $participant;
            }
        }

        return $result;
    }

    public function getWebMeetingNotifiedAt(): ?DateTime
    {
        return $this->webMeetingNotifiedAt;
    }

    public function setWebMeetingNotifiedAt(?DateTime $webMeetingNotifiedAt): void
    {
        $this->webMeetingNotifiedAt = $webMeetingNotifiedAt;
    }

    public function isWebMeetingNotified(): bool
    {
        return null !== $this->webMeetingNotifiedAt;
    }

    public function getWebMeetingUrlToken(): ?string
    {
        return $this->webMeetingUrlToken;
    }

    public function setWebMeetingUrlToken(?string $webMeetingUrlToken): void
    {
        $this->webMeetingUrlToken = $webMeetingUrlToken;
    }

    public function createWebMeetingUrl(): ?string
    {
        return  'https://admin.webmeeting.cz/room/enter?token=' . $this->webMeetingUrlToken;
    }


    public function getWebMeetingId(): ?int
    {
        return $this->webMeetingId;
    }

    public function setWebMeetingId(?int $webMeetingId): void
    {
        $this->webMeetingId = $webMeetingId;
    }

    public function getWebMeetingConfirmedAt(): ?DateTime
    {
        return $this->webMeetingConfirmedAt;
    }

    public function setWebMeetingConfirmedAt(?DateTime $webMeetingConfirmedAt): void
    {
        $this->webMeetingConfirmedAt = $webMeetingConfirmedAt;
    }

    public function getWebMeetingConfirmationCode(): string
    {
        return $this->webMeetingConfirmationCode;
    }

    public function setWebMeetingConfirmationCode(string $webMeetingConfirmationCode): void
    {
        $this->webMeetingConfirmationCode = $webMeetingConfirmationCode;
    }

    /**
     * Generate a random confirmation code
     */
    public function generateWebMeetingConfirmationCode(): void
    {
        $randomBytes = '';
        for ($i = 0; $i < 16; ++$i) {
            $randomBytes .= chr(random_int(0, 255));
        }

        $this->webMeetingConfirmationCode = md5(self::class . $randomBytes);
    }

    public function isWebMeetingConfirmed(): bool
    {
        return null !== $this->webMeetingConfirmedAt;
    }

    public function willAttend(): bool
    {
        return $this->isConfirmed() || $this->isWebMeetingConfirmed();
    }
}
