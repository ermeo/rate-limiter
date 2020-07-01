<?php

namespace Ermeo\RateLimitBundle\Tests\Configuration;

use Ermeo\RateLimitBundle\Configuration\GeneralConfiguration;
use Ermeo\RateLimitBundle\Tests\Fixtures\Rules\ClientRules;
use Ermeo\RateLimitBundle\Tests\Fixtures\Rules\UserRules;
use PHPUnit\Framework\TestCase;

class GeneralConfigurationTest extends TestCase
{
    public function testIsEnabledShouldBeTrueWhenTrueIsSetOnConstructor(): void
    {
        $expected = true;
        $generalConfiguration = new GeneralConfiguration($expected, []);

        $this->assertSame($expected, $generalConfiguration->isEnabled());
    }

    public function testIsEnabledShouldBeFalseWhenFalseIsSetOnConstructor()
    {
        $expected = false;
        $generalConfiguration = new GeneralConfiguration($expected, []);

        $this->assertSame($expected, $generalConfiguration->isEnabled());
    }

    public function testRulesShouldImplementsCheckerInterfaces()
    {
        $rules = [new UserRules(), new ClientRules()];

        $generalConfiguration = new GeneralConfiguration(true, $rules);

        $this->assertSame($rules, $generalConfiguration->getRules());

        foreach ($generalConfiguration->getRules() as $rule) {
            $this->assertTrue(
                method_exists($rule, 'byPass'),
                'Class does not have method byPass'
            );
        }
    }
}
