<?php
/**
 * Copyright 2017 LitGroup, LLC
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

declare(strict_types=1);

namespace Test\LitGroup\Transaction;

use LitGroup\Transaction\Exception\StateException;
use LitGroup\Transaction\Exception\TransactionException;
use LitGroup\Transaction\Transaction;
use LitGroup\Transaction\TransactionHandler;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    function testStartOfTransactionOnInstantiation(): void
    {
        $handler = new SpyHandler();
        new Transaction($handler);

        self::assertSame([SpyHandler::BEGIN], $handler->getCalls());
    }

    function testTransactionExceptionOnBegin(): void
    {
        $exception = new TransactionException();
        $handler = $this->createMock(TransactionHandler::class);
        $handler->method('begin')->willThrowException($exception);

        try {
            new Transaction($handler);
            $this->fail();
        } catch (TransactionException $caught) {
            self::assertSame($exception, $caught);
        }
    }

    function testCommit(): void
    {
        $handler = new SpyHandler();
        $transaction = new Transaction($handler);

        $transaction->commit();
        self::assertSame([SpyHandler::BEGIN, SpyHandler::COMMIT], $handler->getCalls());
    }

    function testTransactionExceptionOnCommit(): void
    {
        $exception = new TransactionException();
        $handler = $this->createMock(TransactionHandler::class);
        $handler->method('commit')->willThrowException($exception);
        $transaction = new Transaction($handler);

        try {
            $transaction->commit();
            $this->fail();
        } catch (TransactionException $caught) {
            self::assertSame($exception, $caught);
        }
    }

    function testRollingBack(): void
    {
        $handler = new SpyHandler();
        $transaction = new Transaction($handler);

        $transaction->rollBack();
        self::assertSame([SpyHandler::BEGIN, SpyHandler::ROLLBACK], $handler->getCalls());
    }

    function testTransactionExceptionOnRollBack(): void
    {
        $exception = new TransactionException();
        $handler = $this->createMock(TransactionHandler::class);
        $handler->method('rollBack')->willThrowException($exception);
        $transaction = new Transaction($handler);

        try {
            $transaction->rollBack();
            $this->fail();
        } catch (TransactionException $caught) {
            self::assertSame($exception, $caught);
        }
    }

    function testStateExceptionOnCommitOfClosedTransaction(): void
    {
        $handler = new SpyHandler();
        $transaction = new Transaction($handler);

        $transaction->commit();
        try {
            $transaction->commit();
            $this->fail();
        } catch (StateException $e) {
            self::assertSame([SpyHandler::BEGIN, SpyHandler::COMMIT], $handler->getCalls());
        }
    }

    function testStateExceptionOnRollingBackOfClosedTransaction(): void
    {
        $handler = new SpyHandler();
        $transaction = new Transaction($handler);

        $transaction->rollBack();
        try {
            $transaction->rollBack();
            $this->fail();
        } catch (StateException $e) {
            self::assertSame([SpyHandler::BEGIN, SpyHandler::ROLLBACK], $handler->getCalls());
        }
    }

    function testClosingOnExceptionDuringCommit(): void
    {
        $handler = $this->createMock(TransactionHandler::class);
        $handler->method('commit')->willThrowException(new TransactionException());
        $transaction = new Transaction($handler);

        try {
            $transaction->commit();
        } catch (TransactionException $e) {}

        $this->expectException(StateException::class);
        $transaction->commit();
    }

    function testClosingOnExceptionDuringRollback(): void
    {
        $handler = $this->createMock(TransactionHandler::class);
        $handler->method('rollBack')->willThrowException(new TransactionException());
        $transaction = new Transaction($handler);

        try {
            $transaction->rollBack();
        } catch (TransactionException $e) {}

        $this->expectException(StateException::class);
        $transaction->rollBack();
    }
}
