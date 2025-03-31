#!/usr/bin/env php
<?php

include_once 'ExpenseManager.php';
include_once 'CommandType.php';
include_once 'CliParser.php';

function displayHelp(): void
{
    echo "Expense Tracker CLI - Manage your expenses with ease.\n\n";
    echo "Usage:\n";
    echo "  php cli.php <command> [options]\n\n";
    echo "Commands:\n";
    echo "  add          Add a new expense (--amount, --description, [--category]).\n";
    echo "  update       Update an expense (--id, [--amount], [--description], [--category]).\n";
    echo "  delete       Delete an expense (--id).\n";
    echo "  list         List all expenses ([--category]).\n";
    echo "  summary      Show expense summary ([--month]).\n";
    echo "  set-budget   Set a monthly budget (--amount, --month).\n";
    echo "  export       Export expenses to a CSV file.\n";
    echo "  --help       Show this help message.\n\n";
    echo "Use 'php cli.php <command> --help' for more details on a specific command.\n";
}

$parser = new CliParser($argv);

$expenseManager = new ExpenseManager();

try {

   $command = $parser->getCommand();
   if ($command === null) {
      displayHelp();
      exit;
   }

   $commandType = CommandType::from($command);

   match ($commandType) {
      CommandType::ADD => $expenseManager->addExpense(
         $parser->getAmount(),
         $parser->getDescription(),
         $parser->getCategory()
      ),
      CommandType::UPDATE => $expenseManager->updateExpense(
         $parser->getId(),
         $parser->getAmount(),
         $parser->getDescription(),
         $parser->getCategory()
      ),
      CommandType::DELETE => $expenseManager->deleteExpense(
         $parser->getId()
      ),
      CommandType::LIST => $expenseManager->getAllExpenses(
         $parser->getCategory() !== null ? strtolower($parser->getCategory()) : null
      ),
      CommandType::SUMMARY => $expenseManager->getSummary($parser->getMonth()),
      CommandType::SET_BUDGET => $expenseManager->setBudget(
         $parser->getAmount(),
         $parser->getMonth()
      ),
      CommandType::EXPORT => $expenseManager->exportExpensesToCSV(),
      CommandType::HELP => displayHelp(),
   };
} catch (ValueError $e) {
   echo "Invalid command. Use '--help' for more information.\n";
}
