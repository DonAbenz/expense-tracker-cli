<?php

enum CommandType: string
{
   case ADD = 'add';
   case UPDATE = 'update';
   case DELETE = 'delete';
   case LIST = 'list';
   case SUMMARY = 'summary';
   case HELP = '--help';
}
