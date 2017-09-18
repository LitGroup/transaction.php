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

namespace LitGroup\Transaction;

final class Transaction
{
    /** @var TransactionHandler */
    private $handler;

    public function __construct(TransactionHandler $handler)
    {
        $this->handler = new TransactionStateHandler($handler);
        $this->begin();
    }

    public function commit(): void
    {
        $this->getHandler()->commit();
    }

    public function rollBack(): void
    {
        $this->getHandler()->rollBack();
    }

    private function getHandler(): TransactionHandler
    {
        return $this->handler;
    }

    private function begin(): void
    {
        $this->getHandler()->begin();
    }
}

/**
 * @internal
 */
class TransactionStateHandler implements TransactionHandler
{
    /** @var TransactionHandler */
    private $handler;

    /** @var bool */
    private $open = false;

    public function __construct(TransactionHandler $handler)
    {
        $this->handler = $handler;
    }

    public function begin(): void
    {
        $this->getHandler()->begin();
        $this->openTransaction();
    }

    public function commit(): void
    {
        if (!$this->transactionIsOpen()) {
            throw new StateException('Cannot commit. Transaction has been already closed.');
        }

        try {
            $this->getHandler()->commit();
        } finally {
            $this->closeTransaction();
        }
    }

    public function rollBack(): void
    {
        if (!$this->transactionIsOpen()) {
            throw new StateException('Cannot roll back. Transaction has been already closed.');
        }

        try {
            $this->getHandler()->rollBack();
        } finally {
            $this->closeTransaction();
        }
    }

    private function getHandler(): TransactionHandler
    {
        return $this->handler;
    }

    private function openTransaction(): void
    {
        $this->open = true;
    }

    private function closeTransaction(): void
    {
        $this->open = false;
    }

    private function transactionIsOpen(): bool
    {
        return $this->open;
    }
}