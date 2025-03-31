<?php

class ExpenseDisplay
{
   public static function print(array $expenses): void
   {
       $headers = ['id', 'date', 'description', 'amount'];
       $widths = self::calculateColumnWidths($expenses, $headers);

       self::printSeparatorLine($widths);
       self::printRow($headers, $widths);
       self::printSeparatorLine($widths);

       foreach ($expenses as $expense) {
           $row = $expense->__toArray();
           $rowData = array_map(fn($header) => $row[$header], $headers);
           self::printRow($rowData, $widths);
       }

       self::printSeparatorLine($widths);
   }

   private static function calculateColumnWidths(array $expenses, array $headers): array
   {
       return array_map(function ($header) use ($expenses) {
           $maxLength = strlen($header);
           foreach ($expenses as $expense) {
               $row = $expense->__toArray();
               $maxLength = max($maxLength, strlen((string) $row[$header]));
           }
           return $maxLength;
       }, $headers);
   }

   private static function printSeparatorLine(array $widths): void
   {
       echo '+';
       foreach ($widths as $width) {
           echo str_repeat('-', $width + 2) . '+';
       }
       echo PHP_EOL;
   }

   private static function printRow(array $row, array $widths): void
   {
       echo '|';
       foreach ($row as $key => $value) {
           printf(" %-{$widths[$key]}s |", $value);
       }
       echo PHP_EOL;
   }
}
