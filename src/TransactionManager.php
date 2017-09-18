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

use function call_user_func;

class TransactionManager
{
    /** @var TransactionHandler */
    private $handler;

    public function __construct(TransactionHandler $handler)
    {
        $this->handler = new SingleActiveTransactionHandler($handler);
    }

    public function beginTransaction(): Transaction
    {
        return new Transaction($this->getHandler());
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function runTransactional(callable $func)
    {
        $transaction = $this->beginTransaction();

        try {
            $result = call_user_func($func);
            $transaction->commit();

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private function getHandler(): TransactionHandler
    {
        return $this->handler;
    }
}

/**
 * @internal
 */
class SingleActiveTransactionHandler implements TransactionHandler
{
    /**
     * @var TransactionHandler
     */
    private $handler;

    private $open = false;

    public function __construct(TransactionHandler $handler)
    {
        $this->handler = $handler;
    }

    public function begin(): void
    {
        if ($this->transactionIsOpen()) {
            throw new StateException('Only one active transaction can exist.');
        }

        $this->openTransaction();
        $this->getHandler()->begin();
    }

    public function commit(): void
    {
        $this->closeTransaction();
        $this->getHandler()->commit();
    }

    public function rollBack(): void
    {
        $this->closeTransaction();
        $this->getHandler()->rollBack();
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