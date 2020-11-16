<?php

namespace Ermeo\RateLimitBundle\Interfaces;

interface CheckerInterface
{
    public function byPass(): bool;
}
