<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\Bundle\SensitiveSerializerBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BroadwaySensitiveSerializerBundleTest extends WebTestCase
{
    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function it_does_not_throw_when_booting_kernel(): void
    {
        static::bootKernel();
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        return new AppKernel('test', true);
    }
}
