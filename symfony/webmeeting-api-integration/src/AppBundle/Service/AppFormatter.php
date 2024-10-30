<?php

namespace AppBundle\Service;

use AppBundle\Component\Helper\MathHelper;
use AppBundle\Entity\ExplicitCurrencyInterface;
use AppBundle\Entity\Seminar;
use DateTime;
use Imatic\Bundle\ViewBundle\Templating\Helper\Format\FormatterInterface;
use IntlDateFormatter;
use InvalidArgumentException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager as ImagineCacheManager;
use Locale;
use NumberFormatter;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Contracts\Translation\TranslatorInterface;
use UnitEnum;

/**
 * Application formatter
 */
class AppFormatter implements FormatterInterface
{

    public function formatDateToWord($value, $options): string
    {
        return
            $this->translator->trans((empty($options['short']) ? 'day' : 'day_short') . ".{$value->format('N')}", [], 'AppBundleTime')
            . " {$value->format('j.n.Y')}";
    }
}
