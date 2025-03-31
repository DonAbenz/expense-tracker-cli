#!/usr/bin/env php
<?php

include_once 'ExpenseManager.php';
include_once 'CommandType.php';
include_once 'CliParser.php';

// Parse the command-line arguments
$parser = new CliParser($argv);
$command = $parser->getCommand();
$amount = $parser->getAmount();
$description = $parser->getDescription();

$expenseManager = new ExpenseManager();

try {
    // Convert the command to an enum
    $commandType = CommandType::from($command);
    match ($commandType) {
        CommandType::ADD => $expenseManager->addExpense($amount, $description),
        CommandType::LIST => $expenseManager->getAllExpenses(),
        default => print("Invalid command. Use '--help' for more information.\n"),
    };
} catch (ValueError $e) {
    echo "Invalid command. Use '--help' for more information.\n";
}
