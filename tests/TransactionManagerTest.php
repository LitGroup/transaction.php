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
use LitGroup\Transaction\Transaction;
use LitGroup\Transaction\TransactionManager;
use PHPUnit\Framework\TestCase;

class TransactionManagerTest extends TestCase
{
    /** @var TransactionManager */
    private $manager;

    /** @var SpyHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->handler = new SpyHandler();
        $this->manager = new TransactionManager($this->handler);
    }

    function testTransaction(): void
    {
        $this->manager->beginTransaction()->commit();
        $this->manager->beginTransaction()->rollBack();
        $this->manager->beginTransaction();

        self::assertSame(
            [
                SpyHandler::BEGIN,
                SpyHandler::COMMIT,
                SpyHandler::BEGIN,
                SpyHandler::ROLLBACK,
                SpyHandler::BEGIN
            ],
            $this->handler->getCalls()
        );
    }

    function testStateExceptionOnDuplicationOfTransaction(): void
    {
        $this->manager->beginTransaction();

        $this->expectException(StateException::class);
        $this->manager->beginTransaction();
    }
}
