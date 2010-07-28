<?php
abstract class sfHarmonyService
{
  protected $async = false;
  protected $operation;
  protected $arguments;
  protected $params;
  
  protected $parser_class;
  protected $parser;
  
  protected $result;

  public function __construct($operation, $params = array())
  {
    $this->async = false;
    $this->operation = $operation;
    $this->params = $params;

    $this->initialize();
  }
  
  abstract protected function initialize();
  
  public function getParser()
  {
    if(is_null($this->parser))
    {
      
      $this->parser = new $this->parser_class();
    }
    
    return $this->parser;
  }

  public function isAsync()
  {
    return $this->async;
  }
  
  public function setAsync($async)
  {
    $this->async = $async;
  }
  
  public function setOperation(string $operation)
  {
    $this->operation = $operation;
  }
  
  public function getOperation()
  {
    return $this->operation;
  }
  
  public function setArguments(array $arguments)
  {
    $this->arguments = $arguments;
  }
  
  public function getArguments()
  {
    return $this->arguments;
  }
  
  public function setParams(array $params)
  {
    $this->params = $params;
  }
  
  public function getParams()
  {
    return $this->params;
  }

  public function setResult($result)
  {
    $this->result = $result;  
  }
  
  public function getResult()
  {
    return $this->result;
  }
  
  abstract public function exec();
}