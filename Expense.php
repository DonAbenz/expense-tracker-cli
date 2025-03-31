<?php

class Expense
{
   public function __construct(
      private int $id,
      private string $description,
      private float $amount,
      private string $category,
      private ?string $date = null
   ) {
      $this->date = $date ?? date('Y-m-d');
   }

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

   public function __call(string $name, array $arguments)
   {
      if (str_starts_with($name, 'get')) {
         $property = lcfirst(substr($name, 3));

         if (property_exists($this, $property)) {
            return $this->$property;
         }
      }

      if (str_starts_with($name, 'set')) {
         $property = lcfirst(substr($name, 3));

         if (property_exists($this, $property)) {
            $this->$property = $arguments[0];
            return;
         }
      }

      throw new BadMethodCallException("Method $name does not exist.");
   }
}
