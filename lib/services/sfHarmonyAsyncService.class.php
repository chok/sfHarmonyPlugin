<?php
abstract class sfHarmonyAsyncService extends sfHarmonyService
{
  protected $callback;

  public function __construct($operation, $params = array())
  {
    parent::__construct($operation, $params);

    $this->setAsync(true);
  }

  public function setCallback($callback)
  {
    if(is_callable($callback))
    {
      $this->callback = $callback;
    }
    else throw new sfException(sprintf('callback "%s" is not callable', print_r($callback, true)));
  }

  public function getCallback()
  {
    return $this->callback;
  }

  public function send($data)
  {
    call_user_func($this->callback, $data);
  }
}