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

use LitGroup\Transaction\Exception\StateException;
use LitGroup\Transaction\Exception\TransactionException;

final class Transaction
{
    /** @var TransactionHandler */
    private $handler;

    /**
     * @throws TransactionException
     */
    public function __construct(TransactionHandler $handler)
    {
        $this->handler = new TransactionStateHandler($handler);
        $this->getHandler()->begin();
    }

    /**
     * @throws TransactionException
     */
    public function commit(): void
    {
        $this->getHandler()->commit();
    }

    /**
     * @throws TransactionException
     */
    public function rollBack(): void
    {
        $this->getHandler()->rollBack();
    }

    private function getHandler(): TransactionHandler
    {
        return $this->handler;
    }
}

/**
 * User internally in the Transaction class to control state of a transaction (open/closed).
 *
 * You should not use it directly in your code.
 *
 * @internal
 */
class TransactionStateHandler implements TransactionHandler
{
    /** @var TransactionHandler */
    private $decorated;

    /** @var bool  */
    private $isOpen = false;

    public function __construct(TransactionHandler $decorated)
    {
        $this->decorated = $decorated;
    }

    public function begin(): void
    {
        $this->getDecorated()->begin();
        $this->open();
    }

    public function commit(): void
    {
        if (!$this->isOpen()) {
            throw new StateException('Closed transaction cannot be committed.');
        }

        try {
            $this->getDecorated()->commit();
        } finally {
            $this->close();
        }
    }

    public function rollBack(): void
    {
        if (!$this->isOpen()) {
            throw new StateException('Closed transaction cannot be rolled back.');
        }

        try {
            $this->getDecorated()->rollBack();
        } finally {
            $this->close();
        }
    }

    private function getDecorated(): TransactionHandler
    {
        return $this->decorated;
    }

    private function isOpen(): bool
    {
        return $this->isOpen;
    }

    private function open(): void
    {
        $this->isOpen = true;
    }

    private function close(): void
    {
        $this->isOpen = false;
    }
};