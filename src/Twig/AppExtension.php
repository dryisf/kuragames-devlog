<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('youtube', [$this, 'formatYoutubeLink']),
        ];
    }

    public function formatYoutubeLink($video)
    {
        list($youtubeLink, $videoId) = explode('?v=', $video);
        return $videoId;
    }
}