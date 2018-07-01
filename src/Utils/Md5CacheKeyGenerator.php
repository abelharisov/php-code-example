<?php

namespace Example\Utils;

class Md5CacheKeyGenerator implements CacheKeyGeneratorInterface
{
    /** @inheritdoc */
    public function getKey(array $data): string
    {
        return md5(json_encode($data));
    }
}
