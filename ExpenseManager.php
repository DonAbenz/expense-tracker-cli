<?php

include_once 'Expense.php';
include_once 'ExpenseDisplay.php';

class ExpenseManager
{
   private $expenses = [];
   private $budget = null;
   private $expenseFilePath = 'expenses.json';
   private $budgetFilePath = 'budget.json';

   public function __construct()
   {
      $this->loadExpenses();
   }

   private function loadExpenses()
   {
      if (!file_exists($this->expenseFilePath)) {
         file_put_contents($this->expenseFilePath, json_encode([]));
      }

      if (!file_exists($this->budgetFilePath)) {
         file_put_contents($this->budgetFilePath, json_encode([]));
      }

      $expenses = json_decode(file_get_contents($this->expenseFilePath), true);

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
      return file_put_contents($this->expenseFilePath, $json) !== false;
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
      $this->checkBudget($amount);
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

   public function setBudget($amount, $month)
   {
      if (empty($amount) || empty($month)) {
         echo "Amount and month are required." . PHP_EOL;
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

      if ($month < 1 || $month > 12) {
         echo "Invalid month. Please provide a month between 1 and 12." . PHP_EOL;
         return;
      }

      $month = str_pad($month, 2, '0', STR_PAD_LEFT);
      $currentYear = date('Y');

      // Ensure $budgets is an array
      $budgets = json_decode(file_get_contents($this->budgetFilePath), true);
      if (!is_array($budgets)) {
         $budgets = [];
      }

      // Check if a budget already exists for the given month and year
      $existingBudget = array_filter($budgets, function ($item) use ($month, $currentYear) {
         return $item['month'] == $month && $item['year'] == $currentYear;
      });

      if (!empty($existingBudget)) {
         echo "Budget already set for month $month." . PHP_EOL;
         return;
      }

      // Add the new budget
      $budget = [
         'month' => $month,
         'year' => $currentYear,
         'amount' => $amount
      ];

      $budgets[] = $budget;

      // Save the updated budgets to the file
      file_put_contents($this->budgetFilePath, json_encode($budgets, JSON_PRETTY_PRINT));
      echo "Budget set successfully for month $month: $" . number_format($amount, 2) . PHP_EOL;
   }

   public function checkBudget($amount)
   {
      $currentMonth = date('m');
      $currentYear = date('Y');

      // Load the budget for the current month
      $budgets = json_decode(file_get_contents($this->budgetFilePath), true);
      if (is_array($budgets)) {
         foreach ($budgets as $budget) {
            if ($budget['month'] == $currentMonth && $budget['year'] == $currentYear) {
               $this->budget = $budget['amount'];
               break;
            }
         }
      }

      if ($this->budget === null) {
         return; // No budget set for the current month
      }

      // Calculate the total expenses for the current month
      $monthlyExpenses = array_filter($this->expenses, function ($expense) use ($currentMonth, $currentYear) {
         $expenseDate = DateTime::createFromFormat('Y-m-d', $expense->getDate());
         return $expenseDate && $expenseDate->format('Y') == $currentYear && $expenseDate->format('m') == $currentMonth;
      });

      $total = array_reduce($monthlyExpenses, function ($carry, $expense) {
         return $carry + $expense->getAmount();
      }, 0);

      // Check if the total exceeds the budget
      if ($total > $this->budget) {
         echo "Warning: You have exceeded your budget of $" . number_format($this->budget, 2) . " for this month!" . PHP_EOL;
      }
   }

   public function exportExpensesToCSV(string $filePath = 'expenses.csv'): void
   {
      if (empty($this->expenses)) {
         echo "No expenses to export." . PHP_EOL;
         return;
      }

      $file = fopen($filePath, 'w');
      if ($file === false) {
         echo "Failed to create the CSV file." . PHP_EOL;
         return;
      }

      fputcsv($file, ['ID', 'Date', 'Description', 'Category', 'Amount'], ',', '"', '\\');

      foreach ($this->expenses as $expense) {
         $data = $expense->__toArray();
         fputcsv($file, [
            $data['id'],
            $data['date'],
            $data['description'],
            $data['category'],
            $data['amount']
         ], ',', '"', '\\');
      }

      fclose($file);
      echo "Expenses exported successfully to $filePath." . PHP_EOL;
   }
}
