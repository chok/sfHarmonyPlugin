<?php
class sfHarmonyServiceDispatcher extends sfHarmonyDispatcher
{
  public function dispatch()
  {
    $packages = explode('.', $this->service);

    $service_path = array();
    $service = null;
    foreach($packages as $package)
    {
      $service_path[] = $package;

      $service_class = 'sf'.sfInflector::classify(implode('_', $service_path)).'Service';
      $package_class = 'sf'.sfInflector::classify($package).'Service';

      if(class_exists($service_class))
      {
        $service = $service_class;
      }
      elseif(class_exists($package_class))
      {
        $service = $package_class;
      }
      else
      {
        break;
      }
    }

    if(is_null($service))
    {
      $service = sfConfig::get('app_harmony_default_service', 'sfHarmonyWebService');
    }

    if(!class_exists($service))
    {
      throw new sfException(sprintf('Service "%s" does not exists', $service));
    }
  
    $params = array();
    $instance = new $service($this->operation);
    
    if($instance->isAsync())
    {
      $instance->setCallback(array($this, 'send'));
      $instance->exec();
    }
    else
    {
      $this->send($instance->exec());
    }
  }

  public function send($result)
  {
    sfHarmonyGateway::getInstance()->addResult($this->request_id, $result);
  }
}