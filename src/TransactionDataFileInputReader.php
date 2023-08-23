<?php

namespace Bokoch\CommissionCalculator;

use Bokoch\CommissionCalculator\Dto\TransactionData;

final readonly class TransactionDataFileInputReader
{
    /**
     * @param string $inputFilePath
     * @return TransactionData[]
     */
    public function getTransactions(string $inputFilePath): array
    {
        $transactionsContent = @file_get_contents($inputFilePath);
        if ($transactionsContent === false) {
            throw new \InvalidArgumentException(
                sprintf('Cannot read transactions data from path: "%s"', $inputFilePath)
            );
        }

        $transactionJsonItems = explode("\n", $transactionsContent);

        $transactions = [];
        foreach ($transactionJsonItems as $transactionJsonItem) {
            if (empty($transactionJsonItem)) {
                continue;
            }


            $transaction = json_decode($transactionJsonItem, true);
            if ($transaction === null) {
                continue;
            }

            $transactions[] = new TransactionData(
                $transaction['bin'],
                (float) $transaction['amount'],
                $transaction['currency'],
            );
        }

        return $transactions;
    }
}