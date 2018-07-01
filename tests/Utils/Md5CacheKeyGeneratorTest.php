<?php

namespace Example\Tests\Utils;

use Example\Utils\Md5CacheKeyGenerator;
use PHPUnit\Framework\TestCase;

class Md5CacheKeyGeneratorTest extends TestCase
{
    /** @var Md5CacheKeyGenerator */
    private $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new Md5CacheKeyGenerator();
    }

    /**
     * @example
     */
    public function testGetKey(): void
    {
        self::assertEquals('2a787701bef2dd6b2e42ec34533a1cca', $this->generator->getKey(['data']));
    }
}
