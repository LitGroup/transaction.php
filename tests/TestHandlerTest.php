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

use LitGroup\Transaction\TestHandler;
use LitGroup\Transaction\TransactionHandler;
use PHPUnit\Framework\TestCase;

class TestHandlerTest extends TestCase
{
    /**
     * @var TestHandler
     */
    private $handler;

    protected function setUp()
    {
        $this->handler = new TestHandler();
    }

    function testInstance(): void
    {
        $this->assertInstanceOf(TransactionHandler::class, $this->handler);
    }

    function testCommitFlow(): void
    {
        $this->handler->begin();
        $this->assertFalse($this->handler->transactionCommitted());

        $this->handler->commit();
        $this->assertTrue($this->handler->transactionCommitted());
    }

    function testRollingBackFlow(): void
    {
        $this->handler->begin();
        $this->assertFalse($this->handler->transactionRolledBack());

        $this->handler->rollBack();
        $this->assertTrue($this->handler->transactionRolledBack());
    }

    function testResetStateOnBegin(): void
    {
        $this->handler->begin();

        $this->handler->commit();
        $this->handler->begin();
        $this->assertFalse($this->handler->transactionCommitted());

        $this->handler->rollBack();
        $this->handler->begin();
        $this->assertFalse($this->handler->transactionRolledBack());
    }

}
