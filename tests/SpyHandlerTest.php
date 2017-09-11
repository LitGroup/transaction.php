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

use LitGroup\Transaction\TransactionHandler;
use PHPUnit\Framework\TestCase;

class SpyHandlerTest extends TestCase
{
    function testCallsLogging(): void
    {
        $handler = new SpyHandler();
        self::assertInstanceOf(TransactionHandler::class, $handler);
        self::assertSame([], $handler->getCalls());

        $handler->begin();
        self::assertSame([SpyHandler::BEGIN], $handler->getCalls());

        $handler->commit();
        self::assertSame([SpyHandler::BEGIN, SpyHandler::COMMIT], $handler->getCalls());

        $handler->rollBack();
        self::assertSame([SpyHandler::BEGIN, SpyHandler::COMMIT, SpyHandler::ROLLBACK], $handler->getCalls());
    }
}