<?php

include_once 'Expense.php';
include_once 'ExpenseDisplay.php';

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

      $this->expenses = array_map(function ($expense) {
         return new Expense(
            $expense['id'],
            $expense['description'],
            $expense['amount'],
            $expense['date']
         );
      }, $expenses);
   }

   public function addExpense($amount, $description)
   {
      if (empty($amount) || empty($description)) {
         echo "Amount and description are required." . PHP_EOL;
         return;
      }

      if (!is_numeric($amount)) {
         echo "Amount must be a number." . PHP_EOL;
         return;
      }

      if ($amount <= 0) {
         echo "Amount must be greater than zero." . PHP_EOL;
         return;
      }

      $id = count($this->expenses) > 0 ? end($this->expenses)->getId() + 1 : 1;
      $expense = new Expense(
         $id,
         $description,
         $amount
      );

      $this->expenses[] = $expense;

      file_put_contents($this->filePath, json_encode(array_map(function ($expense) {
         return $expense->__toArray();
      }, $this->expenses), JSON_PRETTY_PRINT));

      echo "Expense added successfully. (ID: " . $expense->getId() . ")" . PHP_EOL;
   }

   public function getAllExpenses()
   {
      if (empty($this->expenses)) {
         echo "No expenses found." . PHP_EOL;
         return;
      }

      ExpenseDisplay::print($this->expenses);
   }
}
