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

class SpyHandler implements TransactionHandler
{
    const BEGIN = 'begin';
    const COMMIT = 'commit';
    const ROLLBACK = 'rollBack';

    /** @var string[] */
    private $calls = [];

    public function begin(): void
    {
        $this->addCall(self::BEGIN);
    }

    public function commit(): void
    {
        $this->addCall(self::COMMIT);
    }

    public function rollBack(): void
    {
        $this->addCall(self::ROLLBACK);
    }

    /**
     * @return string[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    private function addCall(string $call): void
    {
        assert(in_array($call, [self::BEGIN, self::COMMIT, self::ROLLBACK]));

        $this->calls[] = $call;
    }
}