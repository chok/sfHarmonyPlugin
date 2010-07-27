<?php
class sfHarmonyDispatcher
{
  protected $source;
  protected $operation;
  protected $args;
  protected $request_id;

  public function __construct($source, $operation, $args = array(), $request_id = null)
  {
    $this->source = $source;
    $this->operation = $operation;
    $this->args = $args;
    $this->request_id = $request_id;
  }
}