<?php

declare(strict_types=1);

namespace Test\Matiux\Broadway\SensitiveSerializer\Bundle\SensitiveSerializerBundle\Util;

class Path
{
    public static function test(): string
    {
        return realpath(__DIR__.'/..');
    }

    public static function projectDir(): string
    {
        return realpath(self::test().'/../src/Resources');
    }

    public static function testResources(): string
    {
        return realpath(self::test().'/Resources');
    }
}
