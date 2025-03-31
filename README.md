# Expense Tracker CLI

A command-line application to track and manage your expenses effectively. This tool helps you stay on top of your finances by allowing you to add, update, delete, list, and summarize expenses, set budgets, and export data to CSV files.

Inspired by the [Expense Tracker Project Roadmap](https://roadmap.sh/projects/expense-tracker).

## Features

- **Add Expenses**: Record new expenses with details like amount, description, and category.
- **Update Expenses**: Edit existing expenses by their unique ID.
- **Delete Expenses**: Remove expenses by their unique ID.
- **List Expenses**: Display all expenses or filter them by category.
- **Expense Summary**: Generate a summary of expenses for a specific month or overall.
- **Set Budget**: Define a monthly budget and receive alerts when it is exceeded.
- **Export to CSV**: Export all expenses to a CSV file for external use or analysis.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/DonAbenz/expense-tracker-cli.git
   cd expense-tracker-cli
   ```

2. Ensure PHP is installed on your system (PHP 8.1 or higher is recommended).

3. Make the CLI script executable (optional):
   ```bash
   chmod +x cli.php
   ```

## Usage

Run the CLI tool using the following command:
```bash
php cli.php <command> [options]
```

### Available Commands

- **`add`**: Add a new expense.
  ```bash
  php cli.php add --amount <amount> --description <description> [--category <category>]
  ```

- **`update`**: Update an existing expense.
  ```bash
  php cli.php update --id <id> [--amount <amount>] [--description <description>] [--category <category>]
  ```

- **`delete`**: Delete an expense by ID.
  ```bash
  php cli.php delete --id <id>
  ```

- **`list`**: List all expenses or filter by category.
  ```bash
  php cli.php list [--category <category>]
  ```

- **`summary`**: Show a summary of expenses for a specific month or overall.
  ```bash
  php cli.php summary [--month <month>]
  ```

- **`set-budget`**: Set a monthly budget.
  ```bash
  php cli.php set-budget --amount <amount> --month <month>
  ```

- **`export`**: Export expenses to a CSV file.
  ```bash
  php cli.php export
  ```

- **`--help`**: Display help information.
  ```bash
  php cli.php --help
  ```

## Example Usage

### Adding an Expense
```bash
php cli.php add --amount 100 --description "Dinner with friends" --category "food"
```
**Output:**
```
Expense added successfully. (ID: 1)
```

### Listing Expenses
```bash
php cli.php list
```
**Output:**
```
+----+------------+---------------------+----------+--------+
| id | date       | description         | category | amount |
+----+------------+---------------------+----------+--------+
| 1  | 2025-03-31 | Dinner with friends | food     | 100.00 |
+----+------------+---------------------+----------+--------+
```

### Setting a Budget
```bash
php cli.php set-budget --amount 1000 --month 3
```
**Output:**
```
Budget set successfully for month 03: $1,000.00
```

### Exporting to CSV
```bash
php cli.php export
```
**Output:**
```
Expenses exported successfully to expenses.csv
```

### Updating an Expense
```bash
php cli.php update --id 1 --amount 120 --description "Dinner with family"
```
**Output:**
```
Expense updated successfully. (ID: 1)
```

### Deleting an Expense
```bash
php cli.php delete --id 1
```
**Output:**
```
Expense deleted successfully.
```

### Generating a Summary
```bash
php cli.php summary --month 3
```
**Output:**
```
Total expenses for March 2025: $120.00
```

## File Structure

```
expense-tracker-cli/
├── cli.php               # Main entry point for the CLI application
├── CliParser.php         # Command-line argument parser
├── Expense.php           # Expense model
├── ExpenseManager.php    # Core logic for managing expenses
├── ExpenseDisplay.php    # Utility for displaying expenses in a table format
├── expenses.json         # JSON file to store expenses (auto-generated)
├── budget.json           # JSON file to store budgets (auto-generated)
└── README.md             # Project documentation
```

## Requirements

- PHP 8.1 or higher
- JSON extension enabled
- Write permissions for `expenses.json` and `budget.json`
