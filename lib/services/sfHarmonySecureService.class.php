<?php
abstract class sfHarmonySecureService extends sfHarmonyService
{

  protected function call($callable, $arguments)
  {
    if(!is_callable($callable))
    {
      return null;
    }
    $call = false;

    if(is_array($callable))
    {
      list($class, $method) = $callable;

      if(is_object($class)) $class = get_class($class);

      $config = sfYaml::load(sfConfig::get('sf_root_dir').'/config/harmony.yml');

      $harmony = isset($config['harmony'])?$config['harmony']:array();

      $isset_global_expose = isset($harmony['expose']);
      $global_expose = isset($harmony['expose']) && $harmony['expose'];

      $model = isset($harmony['model'])?$harmony['model']:array();

      $isset_model_expose = isset($model['expose']);
      $model_expose = isset($model['expose']) && $model['expose'];

      $isset_model_expose_methods = isset($model['expose_methods']);
      $model_expose_methods = isset($model['expose_methods']) && $model['expose_methods'];

      $config_class = isset($model[$class])?$model[$class]:array();

      $isset_model_class_expose = isset($config_class['expose']);
      $model_class_expose = isset($config_class['expose']) && $config_class['expose'];

      $isset_model_class_expose_methods = isset($config_class['expose_methods']);
      $model_class_expose_methods = isset($config_class['expose_methods']) && $config_class['expose_methods'];

      if($model_class_expose_methods ||
      (!$isset_model_class_expose_methods && $model_class_expose) ||
      (!$isset_model_class_expose_methods && !$isset_model_class_expose && $model_expose_methods) ||
      (!$isset_model_class_expose_methods && !$isset_model_class_expose && !$isset_model_expose_methods && $model_expose) ||
      (!$isset_model_class_expose_methods && !$isset_model_class_expose && !$isset_model_expose_methods && !$isset_model_expose && $global_expose)
      )
      {
         if(isset($config_class['methods']) && is_array($config_class['methods']) && in_array($method, $config_class['methods']))
         {
            return null;   
         }  
         else return call_user_func_array($callable, $arguments);
        
      }
      else
      {
         if(isset($config_class['methods']) && is_array($config_class['methods']) && in_array($method, $config_class['methods']))
         {
            return call_user_func_array($callable, $arguments);   
         }  
         else return null;
      }
       
    }

    return null;
  }
}