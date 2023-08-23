<?php

namespace Tests;

use Bokoch\CommissionCalculator\TransactionDataFileInputReader;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;

class TransactionDataFileInputReaderTest extends AbstractTestCase
{
    #[Test]
    public function it_throw_invalid_argument_exception_when_not_able_to_read_input_file(): void
    {
        $reader = new TransactionDataFileInputReader();

        $this->expectException(InvalidArgumentException::class);
        $transactions = $reader->getTransactions('foo.txt');
    }

    #[Test]
    public function it_can_parse_from_file_valid_json_splitted_with_endline(): void
    {
        $reader = new TransactionDataFileInputReader();

        $transactions = $reader->getTransactions(__DIR__ . '/test-files/input-files/valid-input.txt');

        $this->assertSame(expected: 5, actual: count($transactions));
    }

    #[Test]
    public function it_will_skip_not_valid_json_items(): void
    {
        $reader = new TransactionDataFileInputReader();

        $transactions = $reader->getTransactions(__DIR__ . '/test-files/input-files/invalid-input.txt');

        $this->assertSame(expected: 1, actual: count($transactions));
    }
}