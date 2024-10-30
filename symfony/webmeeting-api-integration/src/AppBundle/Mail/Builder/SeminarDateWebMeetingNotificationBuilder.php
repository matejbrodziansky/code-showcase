<?php

namespace AppBundle\Mail\Builder;

use AppBundle\Entity\Region;
use AppBundle\Kernel;
use GuzzleHttp\Exception\GuzzleException;
use Imatic\Bundle\DataBundle\Data\Command\CommandExecutorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mime\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Component\Context\Context;
use Symfony\Contracts\Service\Attribute\Required;

class SeminarDateWebMeetingNotificationBuilder extends Builder
{
    use InflectionManagerTrait;

    #[Required]
    public function setCommandExecutor(CommandExecutorInterface $commandExecutor): void
    {
        $this->commandExecutor = $commandExecutor;
    }

    #[Required]
    public function setKernel(Kernel $kernel): void
    {
        $this->kernel = $kernel;
    }

    #[Required]
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }


    protected function configureParameters(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'date',
            'participant',
            'client_service_director_signature',
            'web_meeting_url'
        ]);
        $resolver->setDefaults([
            'day' => null,
            'attachment' => null,
            'documentation_attachment' => null,
            'assistent_name' => null,
            'communication_option' => null
        ]);
    }

    protected function buildContext(Context $context): void
    {
        parent::buildContext($context);
        $context->define([
            'region' => fn(Context $context) => $context['parameters']['date']->getSeminar()->getRegion(),
            'recipients' => function (Context $context) {
                $participant = $context['parameters']['participant'];

                return [$participant->getEmail() => $participant->getName()];
            },
            'subject_template' => '@App/SeminarDate/Mail/web-meeting-notification_subject.txt.twig',
            'body_template' => '@App/SeminarDate/Mail/web-meeting-notification_body.html.twig',
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function build(Context $context): Email
    {
        // update possible missing inflection data on participant
        $this
            ->getInflectionManager()
            ->updateMissingPersonInflections([$context['parameters']['participant']]);

        // build message
        $message = parent::build($context);

        // attach file
        if ($context['parameters']['attachment']) {
            $this->addAttachmentFromFile($message, $context['parameters']['attachment']);
        }

        // attach documentation files
        if (!empty($context['parameters']['documentation_attachment'])) {
            foreach ($context['parameters']['documentation_attachment'] as $attachment) {
                $this->addAttachmentFromFile($message, $attachment);
            }
        }

        $this->attachWebMeetingManual($message, $context['region'], $context['parameters']['communication_option']);

        return $message;
    }

    protected function attachWebMeetingManual(
        Email  $message,
        Region $region,
        string $communicationOption
    ): void
    {
        $webMeetingManualFilePath = null;
        try {
            $webMeetingManualFilePath = $this->kernel
                ->locateResource("@AppBundle/Resources/files/web_meeting/{$communicationOption}_manual_{$region->getCode()->value}.pdf");
        } catch (\Throwable $e) {
            $this->logger->error('Web meeting manual not found for region ' . $region->getCode()->value . '.' . $e->getMessage());
        }

        if ($webMeetingManualFilePath) {
            $webMeetingFile = new File($webMeetingManualFilePath, false);
            $this->addAttachmentFromFile(
                $message,
                $webMeetingFile,
                $this->translator->trans("web_meeting.filename.{$communicationOption}", [], 'AppBundleMail')
                . '.' . $webMeetingFile->getExtension()
            );
        }
    }
}
