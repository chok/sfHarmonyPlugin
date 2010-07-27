<?php
class sfHarmonyDispatcher
{
  protected $service;
  protected $operation;
  protected $request_id;

  public function __construct($service, $operation, $request_id = null)
  {
    $this->service = $service;
    $this->operation = $operation;
    $this->request_id = $request_id;
  }
}