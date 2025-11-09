<?php

namespace App\Message;

use App\Entity\Series;

class SeriesWasCreate
{
    public function __construct(public readonly Series $series)
    {
    }
}
