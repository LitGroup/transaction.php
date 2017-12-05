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

use LitGroup\Transaction\StateException;
use LitGroup\Transaction\TransactionHandler;
use LitGroup\Transaction\TransactionManager;
use PHPUnit\Framework\TestCase;

class TransactionManagerTest extends TestCase
{
    function testTransactionManage(): void
    {
        $handler = new SpyHandler();
        $manager = new TransactionManager($handler);

        $transaction = $manager->beginTransaction();
        $transaction->commit();

        $transaction = $manager->beginTransaction();
        $transaction->rollBack();

        $this->assertSame(
            [SpyHandler::BEGIN, SpyHandler::COMMIT, SpyHandler::BEGIN, SpyHandler::ROLLBACK],
            $handler->getCalls()
        );
    }

    function testOnlyOneTransactionCanExist(): void
    {
        $handler = new SpyHandler();
        $manager = new TransactionManager($handler);

        $manager->beginTransaction();
        try {
            $manager->beginTransaction();
            $this->fail('Only one transaction can exist.');
        } catch (StateException $e) {}

        $this->assertSame([SpyHandler::BEGIN], $handler->getCalls());
    }

    function testTransactionalRun(): void
    {
        $handler = new SpyHandler();
        $manager = new TransactionManager($handler);

        $result = $manager->runTransactional(function () use ($handler) {
            $this->assertSame([SpyHandler::BEGIN], $handler->getCalls());

            return 'some result';
        });

        $this->assertSame('some result', $result);
        $this->assertSame([SpyHandler::BEGIN, SpyHandler::COMMIT], $handler->getCalls());
    }

    function testTransactionalRunException(): void
    {
        $handler = new SpyHandler();
        $manager = new TransactionManager($handler);

        try {
            $manager->runTransactional(function () use ($handler) {
                throw new ExampleException();
            });
            $this->fail('Exception must be rethrown.');
        } catch (ExampleException $e) {}

        $this->assertSame([SpyHandler::BEGIN, SpyHandler::ROLLBACK], $handler->getCalls());
    }

    function testExceptionOnCommitDurinRunTransactional(): void
    {
        $handler = $this->createMock(TransactionHandler::class);
        $handler->expects($this->never())->method('rollBack');
        $handler->method('commit')->willThrowException(new ExampleException());

        $manager = new TransactionManager($handler);
        try {
            $manager->runTransactional(function () use ($handler) {/* Nothing to do */});
            $this->fail('Exception must be rethrown.');
        } catch (ExampleException $e) {}
    }
}
