<?php
declare(strict_types=1);

namespace Test\LitGroup\Transaction\Exception;

use LitGroup\Transaction\Exception\TransactionException;
use PHPUnit\Framework\TestCase;

class TransactionExceptionTest extends TestCase
{
    function testInstance(): void
    {
        $exception = new TransactionException('some message');
        self::assertInstanceOf(\Exception::class, $exception);
        self::assertSame('some message', $exception->getMessage());
        self::assertNull($exception->getPrevious());

        $previous = new \Exception();
        $exception = new TransactionException('some message', $previous);
        self::assertSame('some message', $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
    }
}
