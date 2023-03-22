<?php

declare(strict_types=1);

namespace App\Util;

class ControllerCall
{
  public static function generate(ClearStringInterface $clearStringInterface, array $url): string
  {
      $url = explode('?', $_SERVER['REQUEST_URI']);
      $url = explode('/', $url[getenv('FIRST_VALUE')]);
  
      $controller = '\App\Controllers\\'.ucfirst($clearStringInterface::execute(string: $url[getenv('CONTROLLER_INDICE')])).'Controller';
  
      $controller = preg_replace('/[0-9]+h/', '', $controller);
  
      return $controller;
  }
}