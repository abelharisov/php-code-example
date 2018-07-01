<?php

namespace Example\Tests\Decorator;

use Example\Decorator\CachingDataProvider;
use Example\Integration\DataProviderInterface;
use Example\Utils\CacheKeyGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingDataProviderTest extends TestCase
{
    /** @var CachingDataProvider */
    private $cachingDataProvider;

    /** @var DataProviderInterface|MockObject */
    private $mockDataProvider;

    /** @var CacheItemPoolInterface|MockObject */
    private $cacheItemPool;

    /** @var CacheKeyGeneratorInterface|MockObject */
    private $cacheKeyGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockDataProvider = $this->createMock(DataProviderInterface::class);
        $this->cacheItemPool = $this->createMock(CacheItemPoolInterface::class);
        $this->cacheKeyGenerator = $this->createMock(CacheKeyGeneratorInterface::class);

        $this->cachingDataProvider = new CachingDataProvider(
            $this->mockDataProvider,
            '+1 day',
            $this->cacheItemPool,
            $this->cacheKeyGenerator
        );
    }

    public function testNotCachedRequest(): void
    {
        $request = ['request'];
        $response = ['response'];

        $notCachedItem = $this->createMock(CacheItemInterface::class);
        $notCachedItem->method('isHit')->willReturn(false);
        $notCachedItem->method('set')->willReturn($notCachedItem);
        $notCachedItem->method('expiresAt')->willReturn($notCachedItem);

        $this->cacheItemPool->expects(self::once())->method('getItem')->with('key')->willReturn($notCachedItem);
        $this->cacheKeyGenerator->expects(self::once())->method('getKey')->with($request)->willReturn('key');
        $this->mockDataProvider->expects(self::once())->method('get')->with($request)->willReturn($response);

        self::assertEquals($response, $this->cachingDataProvider->get($request));
    }

    public function testCachedRequest(): void
    {
        $request = ['request'];
        $response = ['response'];

        $cachedItem = $this->createMock(CacheItemInterface::class);
        $cachedItem->method('isHit')->willReturn(true);
        $cachedItem->method('get')->willReturn($response);

        $this->cacheItemPool->expects(self::once())->method('getItem')->with('key')->willReturn($cachedItem);
        $this->cacheKeyGenerator->expects(self::once())->method('getKey')->with($request)->willReturn('key');

        self::assertEquals($response, $this->cachingDataProvider->get($request));
    }
}
