<?php

class CliParser
{
   private array $args;
   private ?string $command = null;
   private ?string $amount = null;
   private ?string $description = null;
   private ?string $id = null;
   private ?string $month = null;

   public function __construct(array $argv)
   {
      $this->args = array_slice($argv, 2);
      $this->command = $argv[1] ?? null;
      $this->parseOptions();
   }

   private function parseOptions(): void
   {
      for ($i = 0; $i < count($this->args); $i++) {
         if ($this->args[$i] === '--amount' && isset($this->args[$i + 1])) {
            $this->amount = $this->args[$i + 1];
            $i++;
         } elseif ($this->args[$i] === '--description' && isset($this->args[$i + 1])) {
            $this->description = $this->args[$i + 1];
            $i++;
         } elseif ($this->args[$i] === '--id' && isset($this->args[$i + 1])) {
            $this->id = $this->args[$i + 1];
            $i++;
         } elseif ($this->args[$i] === '--month' && isset($this->args[$i + 1])) {
            $this->month = $this->args[$i + 1];
            $i++;
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

   public function getId(): ?string
   {
      return $this->id;
   }

   public function getMonth(): ?string
   {
      return $this->month;
   }
}
