<?php

namespace GuzzleHttp\Profiling\Unit\Stubs;

use GuzzleHttp\Profiling\DescriptionMaker as DescriptionMakerTrait;

class DescriptionMaker
{
    use DescriptionMakerTrait {
        describe as public;
    }
}
