<?php

include_once 'Expense.php';

class ExpenseManager
{
   private $expenses = [];
   private $filePath = 'expenses.json';

   public function __construct()
   {
      $this->loadExpenses();
   }

   private function loadExpenses()
   {
      if (!file_exists($this->filePath)) {
         file_put_contents($this->filePath, json_encode([]));
      }

      $expenses = json_decode(file_get_contents($this->filePath), true);
      foreach ($expenses as $expense) {
         array_push($this->expenses, new Expense(
            $expense['id'],
            $expense['date'],
            $expense['description'],
            $expense['amount']
         ));
      }
   }

   public function getAllExpenses()
   {
      if (empty($this->expenses)) {
         echo "No expenses found." . PHP_EOL;
         return;
      }

      $this->displayExpenses($this->expenses);
   }

   private function displayExpenses(array $expenses): void
   {
      $headers = ['id', 'date', 'description', 'amount'];
      $widths = $this->calculateColumnWidths($expenses, $headers);

      $this->printSeparatorLine($widths);
      $this->printRow($headers, $widths);
      $this->printSeparatorLine($widths);

      foreach ($expenses as $expense) {
         $row = $expense->__toArray();
         $rowData = array_map(fn($header) => $row[$header], $headers);
         $this->printRow($rowData, $widths);
      }

      $this->printSeparatorLine($widths);
   }

   private function calculateColumnWidths(array $expenses, array $headers): array
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

   private function printSeparatorLine(array $widths): void
   {
      echo '+';
      foreach ($widths as $width) {
         echo str_repeat('-', $width + 2) . '+';
      }
      echo PHP_EOL;
   }

   private function printRow(array $row, array $widths): void
   {
      echo '|';
      foreach ($row as $key => $value) {
         printf(" %-{$widths[$key]}s |", $value);
      }
      echo PHP_EOL;
   }
}
