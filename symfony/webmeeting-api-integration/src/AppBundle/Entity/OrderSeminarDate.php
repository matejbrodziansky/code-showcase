<?php

namespace AppBundle\Entity;

use AppBundle\Enum\CourseType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
class OrderSeminarDate implements ExplicitCurrencyInterface, Stringable
{
    use IdTrait;

    #[
        ORM\Column(
            type: 'string',
            nullable: true,
            enumType: CourseType::class,
        ),
        Assert\Type(
            CourseType::class
        ),
    ]
    private ?CourseType $courseType = null;

    #[
        ORM\JoinColumn(
            nullable: false,
        ),
        ORM\ManyToOne(
            targetEntity: Order::class,
            inversedBy: 'dates',
        ),
        Assert\NotNull(),
        Assert\Valid(),
    ]
    private Order $order;


    #[
        ORM\OneToMany(
            mappedBy: 'date',
            targetEntity: SeminarParticipant::class,
            cascade: ['persist'],
            orphanRemoval: true
        ),
        ORM\OrderBy(
            ['lastName' => 'ASC']
        ),
        Assert\Valid(),
    ]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->datePrice = 0;
        $this->discount = 0;
        $this->expectedParticipantCount = 0;
    }


    public function canSendWebMeetingNotification(): bool
    {
        return $this->courseType->value !== CourseType::ONLINE_PARTICIPATION && $this->date->getWebMeetingStart() !== null;
    }

    /**
     * @return SeminarParticipant[]
     */
    public function getWebMeetingNotificationRecipients(bool $filterOutNotified = false): array
    {
        return SeminarParticipant::filterWebMeetingNotificationRecipients($this->participants->toArray(), $filterOutNotified);
    }
}
