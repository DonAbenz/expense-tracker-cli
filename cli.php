#!/usr/bin/env php
<?php

include_once 'ExpenseManager.php';

$command = $argv[1] ?? null;

$expenseManager = new ExpenseManager();

match ($command) {
   'list' => $expenseManager->getAllExpenses(),
};
