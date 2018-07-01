<?php

namespace Example\Tests\Decorator;

use Example\Decorator\LoggingDataProvider;
use Example\Integration\DataProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggingDataProviderTest extends TestCase
{
    /** @var LoggingDataProvider */
    private $loggingDataProvider;

    /** @var LoggerInterface|MockObject */
    private $mockLogger;

    /** @var DataProviderInterface|MockObject */
    private $mockDataProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockDataProvider = $this->createMock(DataProviderInterface::class);

        $this->loggingDataProvider = new LoggingDataProvider(
            $this->mockDataProvider,
            $this->mockLogger
        );
    }

    public function testLogException(): void
    {
        $request = ['request'];
        $exception = new \Exception();

        $this->mockDataProvider->expects(self::once())->method('get')->with($request)->willThrowException($exception);
        $this->mockLogger->expects(self::once())->method('critical')->with('DataProvider error', ['exception' => $exception]);

        $this->expectException(\Exception::class);

        $this->loggingDataProvider->get($request);
    }
}
