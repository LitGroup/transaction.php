<?php
declare(strict_types=1);

namespace LitGroup\Transaction;

/**
 * Null-object of type TransactionHandler.
 *
 * @codeCoverageIgnore
 */
class NullTransactionHandler implements TransactionHandler
{
    public function begin(): void {}

    public function commit(): void {}

    public function rollBack(): void {}
}