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

class TransactionManager
{
    /** @var TransactionHandler */
    private $handler;

    public function __construct(TransactionHandler $handler)
    {
        $this->handler = new SerialTransactionHandler($handler);
    }

    /**
     * Starts a transaction.
     *
     * New transaction cannot be created before previous will be closed.
     *
     * @return Transaction
     */
    public function beginTransaction(): Transaction
    {
        return new Transaction($this->getHandler());
    }

    private function getHandler(): TransactionHandler
    {
        return $this->handler;
    }
}

/**
 * Allows only one transaction to be open.
 *
 * @internal
 */
class SerialTransactionHandler implements TransactionHandler
{
    /** @var TransactionHandler */
    private $handler;

    /** @var bool */
    private $transactionIsOpen = false;

    public function __construct(TransactionHandler $handler)
    {
        $this->handler = $handler;
    }

    public function begin(): void
    {
        if ($this->transactionIsOpen()) {
            throw new StateException('Transaction has been already started.');
        }

        $this->getHandler()->begin();
        $this->setTransactionIsOpen(true);
    }

    public function commit(): void
    {
        try {
            $this->getHandler()->commit();
        } finally {
            $this->setTransactionIsOpen(false);
        }
    }

    public function rollBack(): void
    {
        try{
            $this->getHandler()->rollBack();
        } finally {
            $this->setTransactionIsOpen(false);
        }
    }

    private function getHandler(): TransactionHandler
    {
        return $this->handler;
    }

    private function transactionIsOpen(): bool
    {
        return $this->transactionIsOpen;
    }

    private function setTransactionIsOpen(bool $value): void
    {
        $this->transactionIsOpen = $value;
    }
}