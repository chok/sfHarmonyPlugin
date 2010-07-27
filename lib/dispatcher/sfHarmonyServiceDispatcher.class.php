<?php
class sfHarmonyServiceDispatcher extends sfHarmonyDispatcher
{
  public function dispatch()
  {
    $call = explode('::', $this->source);

    $packages = array();
    $class = '';
    if(!isset($call[1]))
    {
      $class = $call[0];
    }
    else
    {
      $packages = explode('.', $call[0]);
      $class = $call[1];
    }

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
      $service = sfConfig::get('app_harmony_default_service', 'sfHarmonyService');
    }

    if(!class_exists($service))
    {
      throw new sfException(sprintf('Service "%s" does not exists', $service));
    }

    $instance = new $service($this->source, $this->operation, $this->args);

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