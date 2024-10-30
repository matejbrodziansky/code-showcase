<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Form\OrderTrait;
use AppBundle\Entity\SeminarParticipant;
use Symfony\Contracts\Translation\TranslatorInterface;

class WebMeetingNotificationType extends SeminarParticipantEmailChoiceType
{
    use OrderTrait;

    public function __construct(private TranslatorInterface $translator)
    {
        parent::__construct($translator);
    }

    public function getBlockPrefix(): string
    {
        return 'app_seminar_participant_web_meeting_notification_email_choice';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('communication_option', ChoiceType::class, [
                'choices' => [
                    'chat' => 'chat',
                    'microphone' => 'microphone',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'communication_type',
                'translation_domain' => 'AppBundleSeminarWebMeetingParticipantNotification',
                'data' => 'chat',
                'required' => true
            ])
            ->add('client_service_director_signature', TextareaType::class, [
                'attr' => ['rows' => 5],
                'required' => true,
                'label' => 'options.client_service_director_signature',
                'translation_domain' => 'AppBundleRegion',
                'constraints' => [
                    new Assert\NotBlank(['groups' => ['None']]),
                ],
            ])
            ->add('documentation_attachment', FileType::class, [
                'required' => false,
                'label' => 'Email documentation attachment',
                'help' => 'Email attachment help',
                'multiple' => true,
                'constraints' => [
                    new Assert\File(['maxSize' => '5M']),
                ],
            ]);

        $builder->remove('attachment');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $e) use ($options) {
            if (null === $e->getData()) {
                $e->setData([
                    'participants' => SeminarParticipant::filterWebMeetingNotificationRecipients($options['participants'], true),
                    'client_service_director_signature' => $options['region']->getOption('client_service_director_signature'),
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['participants', 'region']);

        $resolver->setDefaults([
            'submit_label' => 'Send notifications',
            'help' => 'Send webMeeting notifications help',
        ]);
    }

    protected function getOrderMap(): array
    {
        return ['attachment' => 99, 'submit' => 100];
    }

}
