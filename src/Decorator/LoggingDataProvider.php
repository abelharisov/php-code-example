<?php

namespace Example\Decorator;

use Example\Integration\DataProviderInterface;
use Psr\Log\LoggerInterface;

class LoggingDataProvider implements DataProviderInterface
{
    /** @var DataProviderInterface */
    private $dataProvider;
    /** @var LoggerInterface */
    private $logger;

    /**
     * LoggingDataProvider constructor.
     * @param DataProviderInterface $dataProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        DataProviderInterface $dataProvider,
        LoggerInterface $logger
    ) {
        $this->dataProvider = $dataProvider;
        $this->logger = $logger;
    }

    /**
     * @param array $request
     * @return array
     * @throws \Exception
     */
    public function get(array $request): array
    {
        try {
            return $this->dataProvider->get($request);
        } catch (\Exception $e) {
            $this->logger->critical('DataProvider error', ['exception' => $e]);
            throw $e;
        }
    }
}
