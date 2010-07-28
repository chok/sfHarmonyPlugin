<?php
class sfHarmonyWebService extends sfHarmonySecureService
{
  protected function initialize()
  {
    $this->parser_class = 'sfHarmonyWebParser';  
  }
  
  public function exec()
  {
    $stack = $this->getParser()->parse($this->getOperation());
    $first = array_shift($stack);
    
    $class = $first['operation'];

    if(!class_exists($class))
    {
      throw new sfException(sprintf('Class "%s" does not exists', $class));
    }
    
    $result = null;
    
    foreach($stack as $i => $operation)
    {
      if($i == 0)
      {
        if($operation['static'])
        {
          $result = $class;
        }
        else 
        {
          //params à gérer pour lier a un objet -- sfContext::getInstance()->getConfiguration()->getPlugins();
          $result = new $class();
        }    
      }
      $callable = array($result, $operation['operation']);
      if(is_callable(array($result, $operation['operation'])))
      {
        $result = $this->call($callable, $operation['arguments']);
      }
    }

    
    $data = new sfHarmonySecureFormatter($result);
    
    return $data->getRawValue();
  }
}