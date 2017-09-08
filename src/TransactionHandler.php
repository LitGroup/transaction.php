<?php
declare(strict_types=1);

namespace LitGroup\Transaction;

use LitGroup\Transaction\Exception\TransactionException;

interface TransactionHandler
{
    /**
     * @throws TransactionException
     */
    public function begin(): void;

    /**
     * @throws TransactionException
     */
    public function commit(): void;

    /**
     * @throws TransactionException
     */
    public function rollBack(): void;
}