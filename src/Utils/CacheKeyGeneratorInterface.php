<?php

namespace Example\Utils;

interface CacheKeyGeneratorInterface
{
    /**
     * @param array $data
     *
     * @return string
     */
    public function getKey(array $data): string;
}
