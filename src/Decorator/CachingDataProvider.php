<?php

namespace Example\Decorator;

use DateTime;
use Example\Integration\DataProviderInterface;
use Example\Utils\CacheKeyGeneratorInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingDataProvider implements DataProviderInterface
{
    /** @var string */
    private $expireTime;
    /** @var DataProviderInterface */
    private $dataProvider;
    /** @var CacheItemPoolInterface */
    private $cacheItemPool;
    /** @var CacheKeyGeneratorInterface */
    private $cacheKeyGenerator;

    /**
     * CacheDataProvider constructor.
     * @param DataProviderInterface $dataProvider
     * @param string $expireTime
     *  E.g: +1 day
     * @param CacheItemPoolInterface $cacheItemPool
     * @param CacheKeyGeneratorInterface $cacheKeyGenerator
     */
    public function __construct(
        DataProviderInterface $dataProvider,
        string $expireTime,
        CacheItemPoolInterface $cacheItemPool,
        CacheKeyGeneratorInterface $cacheKeyGenerator
    ) {
        $this->expireTime = $expireTime;
        $this->dataProvider = $dataProvider;
        $this->cacheItemPool = $cacheItemPool;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
    }

    /**
     * @inheritdoc
     */
    public function get(array $request): array
    {
        $cacheKey = $this->cacheKeyGenerator->getKey($request);
        $cacheItem = $this->cacheItemPool->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = $this->dataProvider->get($request);

        $expireDate = (new DateTime())->modify($this->expireTime);
        $cacheItem
            ->set($result)
            ->expiresAt($expireDate);

        return $result;
    }
}
