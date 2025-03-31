<?php

class CliParser
{
    private array $args;
    private ?string $command = null;
    private ?string $amount = null;
    private ?string $description = null;

    public function __construct(array $argv)
    {
        $this->args = array_slice($argv, 2); // Remove script name and command
        $this->command = $argv[1] ?? null; // Extract the command
        $this->parseOptions();
    }

    private function parseOptions(): void
    {
        for ($i = 0; $i < count($this->args); $i++) {
            if ($this->args[$i] === '--amount' && isset($this->args[$i + 1])) {
                $this->amount = $this->args[$i + 1];
                $i++; // Skip the next argument since it's the value
            } elseif ($this->args[$i] === '--description' && isset($this->args[$i + 1])) {
                $this->description = $this->args[$i + 1];
                $i++; // Skip the next argument since it's the value
            }
        }
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}