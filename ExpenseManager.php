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

   private function saveExpensesToFile()
   {
      $data = array_map(fn(Expense $expense) => $expense->__toArray(), $this->expenses);
      $json = json_encode(array_values($data), JSON_PRETTY_PRINT);
      return file_put_contents($this->filePath, $json) !== false;
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

      if (!$this->saveExpensesToFile()) {
         echo "Failed to save expenses to file." . PHP_EOL;
         return;
      }

      echo "Expense added successfully. (ID: " . $expense->getId() . ")" . PHP_EOL;
   }

   public function updateExpense($id, $amount, $description)
   {
      if (empty($id) || empty($amount) || empty($description)) {
         echo "ID, amount, and description are required." . PHP_EOL;
         return;
      }

      if (!is_numeric($id) || !is_numeric($amount)) {
         echo "ID and amount must be numbers." . PHP_EOL;
         return;
      }

      if ($amount <= 0) {
         echo "Amount must be greater than zero." . PHP_EOL;
         return;
      }

      $expense = array_filter($this->expenses, function ($expense) use ($id) {
         return $expense->getId() == $id;
      });

      if (empty($expense)) {
         echo "Expense with ID $id not found." . PHP_EOL;
         return;
      }

      $expense = reset($expense);
      $expense->setDescription($description);
      $expense->setAmount($amount);

      if (!$this->saveExpensesToFile()) {
         echo "Failed to save expenses to file." . PHP_EOL;
         return;
      }

      echo "Expense updated successfully. (ID: " . $expense->getId() . ")" . PHP_EOL;
   }

   public function deleteExpense($id)
   {
      if (empty($id)) {
         echo "ID is required." . PHP_EOL;
         return;
      }

      if (!is_numeric($id)) {
         echo "ID must be a number." . PHP_EOL;
         return;
      }

      $expense = array_filter($this->expenses, function ($expense) use ($id) {
         return $expense->getId() == $id;
      });

      if (empty($expense)) {
         echo "Expense with ID $id not found." . PHP_EOL;
         return;
      }

      $this->expenses = array_filter($this->expenses, function ($expense) use ($id) {
         return $expense->getId() != $id;
      });

      if (!$this->saveExpensesToFile()) {
         echo "Failed to save expenses to file." . PHP_EOL;
         return;
      }

      echo "Expense deleted successfully." . PHP_EOL;
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
