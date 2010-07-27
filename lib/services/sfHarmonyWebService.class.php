<?php
class sfHarmonyWebService extends sfHarmonySecureService
{
  protected function initialize()
  {
    $this->parser_class = 'sfHarmonyWebParser';  
  }
  
  public function exec()
  {
    $query = $this->getParser()->parse($this->getOperation());
    
    $first = array_shift($query);
    
    $class = $first['operation'];

    if(!class_exists($class))
    {
      throw new sfException(sprintf('Class "%s" does not exists', $class));
    }

    
      
    
    $service_instance = new $class_name();
    
    sfContext::getInstance()->getLogger()->info('{sfHarmonyPlugin} Call -> Service 4: '.$service_path.$class_name);
    $callable = array($service_instance, $this->operation);
    if(!is_callable($callable))
    {
      sfContext::getInstance()->getLogger()->alert('{sfHarmonyPlugin} Service class does not have '.$this->operation.' method');
      throw new Exception('Service class does not have '.$this->operation.' method');
    }

    $result = call_user_func_array($callable, $this->arguments);

    //return new SabreAMF_ArrayCollection(array(1,2,3));
    //return 'Hello!!';
    $data = new sfHarmonySecureFormatter($result);
    sfContext::getInstance()->getLogger()->info('{sfHarmonyPlugin} Data '.print_r($data->getRawValue(), true));

    return $data->getRawValue();
  }
}