<?php
declare(strict_types=1);

namespace LitGroup\Transaction\Exception;

class TransactionException extends \Exception
{
    public function __construct(string $message = '', \Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}