<?php

namespace AppBundle\Form\Type;

use AppBundle\Data\Query\OnlineMeeting\OnlineMeetingListQuery;
use AppBundle\Data\Query\Venue\VenueListQuery;
use AppBundle\Entity\Seminar;
use AppBundle\Entity\SeminarDate;
use AppBundle\Enum\CourseType;
use AppBundle\Form\DataMapper\SeminarDateDataMapper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Count;

class SeminarDateType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'app_seminar_date';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $seminar = $options['seminar'];
        $builder
            ->setDataMapper(new SeminarDateDataMapper($options['seminar']))

            ->add(
                'webMeetingId',
                IntegerType::class,
                [
                    'help' => 'help.webMeeting',
                    'label' => 'Webmeeting id',
                    'disabled' => $options['is_edit']
                ]
            )
            ->add(
                'webMeetingStart',
                TimeType::class,
                [
                    'attr' => [
                        'class' => 'seminar-date-start-date',
                    ],
                    'help' => 'help.webMeeting',
                    'label' => 'Webmeeting start',
                ])
            ->add(
                'webMeetingEnd',
                TimeType::class,
                [
                    'attr' => [
                        'class' => 'seminar-date-start-date',
                    ],
                    'help' => 'help.webMeeting',
                    'label' => 'Webmeeting end',
                ]);

        if ($options['save_button']) {
            $builder->add('save', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
        }

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            $data = $event->getData();

            if (

                $data->getAllowOnlineParticipation() === true
                &&
                (
                    $data->getWebMeetingStart() === null
                    || $data->getWebMeetingEnd() === null
                )
            ) {
                $form->get('webMeetingStart')->addError(new FormError(''));
                $form->get('webMeetingEnd')->addError(new FormError(''));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['seminar']);

        $resolver->setDefaults([
            'data_class' => SeminarDate::class,
            'empty_data' => null,
            'translation_domain' => 'AppBundleSeminarDate',
            'save_button' => true,
            'is_edit' => false,
        ]);
    }
}
