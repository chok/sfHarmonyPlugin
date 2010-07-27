<?php
abstract class sfHarmonyParser
{
  protected $data;
  
  public function __construct($data)
  {
    $this->data = $data;  
  }
  
  abstract public function parse($data = null);
}