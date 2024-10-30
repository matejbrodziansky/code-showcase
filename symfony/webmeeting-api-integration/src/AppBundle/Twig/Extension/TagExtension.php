<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\PersonInflectionInterface;
use AppBundle\Enum\Gender;
use AppBundle\Component\Util\DefaultArrayObject;
use ArrayObject;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;

/**
 * Appliction tag extension
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class TagExtension extends TwigExtension
implements ExtensionInterface
{
    private array $tags = [

        'styledLinkWithBackground' => ['format' => 'html'],
    ];
    private array $escapers = [
        'html' => [self::class, 'htmlEscaper'],
    ];

    public function __construct(private TranslatorInterface $translator, private bool $debug)
    {
    }

    public function getName(): string
    {
        return 'app_tag';
    }



    /**
     * Expand the backgroundColor tag
     *
     * Syntax:
     *
     *      $backgroundColor($color; $text)
     *
     * Arguments:
     *
     *      $color: the CSS color (e.g. red, #ffffff)
     *      $text:  text to wrap with background color
     */
    private function expandStyledLinkWithBackgroundTag(ArrayObject $args): string
    {
        $backgroundColor = self::e($args[0]);
        $linkUrl = self::e($args[1]);
        $linkText = self::e($args[2]);
        $textColor = isset($args[3]) ? $this->e($args[3]) : 'black';

        return <<<HTML
<span style="background-color:{$backgroundColor}; padding: 7px; font-size: 16px;"><strong><a style="color:{$textColor}; text-decoration: none;" href="{$linkUrl}">{$linkText}</a></strong></span>
HTML;
    }

}
