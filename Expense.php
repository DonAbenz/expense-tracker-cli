<?php

class Expense
{
   public function __construct(
      private int $id,
      private string $date,
      private string $description,
      private float $amount
   ) {}

   public function __toArray(): array
   {
      $array = get_object_vars($this);
      $formattedArray = [];

      foreach ($array as $key => $value) {
         $cleanKey = preg_replace('/^.*\0/', '', $key);
         $formattedArray[$cleanKey] = $value;
      }

      return $formattedArray;
   }
}
