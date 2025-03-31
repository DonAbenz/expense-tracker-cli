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
            $expense['category'],
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

   public function addExpense($amount, $description, $category = null)
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
         $amount,
         $category
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

   public function getAllExpenses(?string $category = null)
   {
      $filteredExpenses = $this->expenses;

      if ($category !== null) {
         $filteredExpenses = array_filter($this->expenses, function ($expense) use ($category) {
            return $expense->getCategory() === $category;
         });
      }

      if (empty($filteredExpenses)) {
         echo "No expenses found." . PHP_EOL;
         return;
      }

      ExpenseDisplay::print($filteredExpenses);
   }

   public function getSummary(?int $month = null)
   {
      if (empty($this->expenses)) {
         echo "No expenses found." . PHP_EOL;
         return;
      }

      $currentYear = date('Y');
      $filteredExpenses = $this->expenses;

      // Filter expenses by month if a month is provided
      if ($month !== null) {
         if ($month < 1 || $month > 12) {
            echo "Invalid month. Please provide a month between 1 and 12." . PHP_EOL;
            return;
         }

         $filteredExpenses = array_filter($this->expenses, function ($expense) use ($month, $currentYear) {
            $expenseDate = DateTime::createFromFormat('Y-m-d', $expense->getDate());
            return $expenseDate && $expenseDate->format('Y') == $currentYear && $expenseDate->format('m') == str_pad($month, 2, '0', STR_PAD_LEFT);
         });
      }

      if (empty($filteredExpenses)) {
         echo $month !== null
            ? "No expenses found for the specified month." . PHP_EOL
            : "No expenses found." . PHP_EOL;
         return;
      }

      $total = array_reduce($filteredExpenses, function ($carry, $expense) {
         return $carry + $expense->getAmount();
      }, 0);

      if ($month !== null) {
         $monthName = DateTime::createFromFormat('!m', $month)->format('F');
         echo "Total expenses for $monthName $currentYear: $" . number_format($total, 2) . PHP_EOL;
         return;
      }

      echo "Total expenses: $" . number_format($total, 2) . PHP_EOL;
   }
}
