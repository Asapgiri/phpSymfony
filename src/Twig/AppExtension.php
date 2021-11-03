<?php
namespace App\Twig;

use App\Service\AdService;
use App\Service\ForumService;
use App\Service\PublicationService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension {
    public function getFunctions()
    {
        return [
            new TwigFunction('getPubs', [PublicationService::class, 'getVisiblePublications']),
            new TwigFunction('getForums', [ForumService::class, 'getAllVisibleForums']),
            new TwigFunction('adsCnt', [AdService::class, 'countUnwatchedAds']),
            new TwigFunction('dateHu', [$this, 'dateHu']),
            new TwigFunction('oldDateHu', [$this, 'oldDateHu'])
        ];
    }

    public function getFilters()
    {
        return [ new TwigFilter('fdateHu', [$this, 'fdateHu'])];
    }

    public function dateHu(string $format) {
        return str_replace([
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ],
        [
            'Január',
            'Február',
            'Március',
            'Április',
            'Május',
            'Június',
            'Július',
            'Augusztus',
            'Szeptember',
            'Október',
            'November',
            'December'
        ],
        date($format));
    }

    public function fdateHu(string $date) {
        return str_replace([
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ],
        [
            'Január',
            'Február',
            'Március',
            'Április',
            'Május',
            'Június',
            'Július',
            'Augusztus',
            'Szeptember',
            'Október',
            'November',
            'December'
        ], $date);
    }

    public function oldDateHu() {
        return str_replace([
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ],
            [
                'Boldogasszony hava
                FERGETEG hava',
                'Böjtelő hava
                JÉGBONTÓ hava',
                'Böjtmás hava
                KIKELET hava',
                'Szent György hava
                SZELEK hava',
                'Pünkösd hava
                ÍGÉRET hava',
                'Szent Iván hava
                NAPISTEN hava',
                'Szent Jakab hava
                ÁLDÁS hava',
                'Kisasszony hava
                ÚJKENYÉR hava',
                'Szent Mihály hava
                FÖLDANYA hava',
                'Mindszent hava
                MAGVETŐ hava',
                'Szent András hava
                ENYÉSZET hava',
                'Karácsony hava
                ÁLOM hava'
            ],
            date("F"));
    }
}