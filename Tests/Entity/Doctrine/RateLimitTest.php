<?php

namespace Ermeo\RateLimitBundle\Tests\Entity\Doctrine;

use Ermeo\RateLimitBundle\Entity\Doctrine\RateLimit;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RateLimitTest extends TestCase
{
    /**
     * @var RateLimit
     */
    protected $rateLimit;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->rateLimit = new RateLimit();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->rateLimit);
    }

    public function testGetLimit(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(RateLimit::class))
            ->getProperty('limit');
        $property->setAccessible(true);
        $property->setValue($this->rateLimit, $expected);
        $this->assertSame($expected, $this->rateLimit->getLimit());
    }

    public function testSetLimit(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(RateLimit::class))
            ->getProperty('limit');
        $property->setAccessible(true);
        $this->rateLimit->setLimit($expected);
        $this->assertSame($expected, $property->getValue($this->rateLimit));
    }

    public function testGetPeriod(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(RateLimit::class))
            ->getProperty('period');
        $property->setAccessible(true);
        $property->setValue($this->rateLimit, $expected);
        $this->assertSame($expected, $this->rateLimit->getPeriod());
    }

    public function testSetPeriod(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(RateLimit::class))
            ->getProperty('period');
        $property->setAccessible(true);
        $this->rateLimit->setPeriod($expected);
        $this->assertSame($expected, $property->getValue($this->rateLimit));
    }
}
