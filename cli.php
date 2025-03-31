#!/usr/bin/env php
<?php

include_once 'ExpenseManager.php';
include_once 'CommandType.php';
include_once 'CliParser.php';

$parser = new CliParser($argv);

$expenseManager = new ExpenseManager();

try {
   // Convert the command to an enum
   $commandType = CommandType::from($parser->getCommand());
   match ($commandType) {
      CommandType::ADD => $expenseManager->addExpense(
         $parser->getAmount(),
         $parser->getDescription()
      ),
      CommandType::UPDATE => $expenseManager->updateExpense(
         $parser->getId(),
         $parser->getAmount(),
         $parser->getDescription()
      ),
      CommandType::LIST => $expenseManager->getAllExpenses(),
      default => print("Invalid command. Use '--help' for more information.\n"),
   };
} catch (ValueError $e) {
   echo "Invalid command. Use '--help' for more information.\n";
}
