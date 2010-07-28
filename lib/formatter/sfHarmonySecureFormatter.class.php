<?php
class sfHarmonySecureFormatter extends sfHarmonyFormatter
{
  protected function convert($data)
  {
    $result = parent::convert($data);
    
    if(is_object($data))
    {
      $class = get_class($data);
      
      $config = sfYaml::load(sfConfig::get('sf_root_dir').'/config/harmony.yml');

      $harmony = isset($config['harmony'])?$config['harmony']:array();

      //TODO DRY! sfHarmonySecureService
      $isset_global_expose = isset($harmony['expose']);
      $global_expose = isset($harmony['expose']) && $harmony['expose'];

      $model = isset($harmony['model'])?$harmony['model']:array();

      $isset_model_expose = isset($model['expose']);
      $model_expose = isset($model['expose']) && $model['expose'];

      $isset_model_expose_fields = isset($model['expose_fields']);
      $model_expose_fields = isset($model['expose_fields']) && $model['expose_fields'];

      $config_class = isset($model[$class])?$model[$class]:array();

      $isset_model_class_expose = isset($config_class['expose']);
      $model_class_expose = isset($config_class['expose']) && $config_class['expose'];

      $isset_model_class_expose_fields = isset($config_class['expose_fields']);
      $model_class_expose_fields = isset($config_class['expose_fields']) && $config_class['expose_fields'];
      
      $fields = isset($config_class['fields']) && is_array($config_class['fields'])?$config_class['fields']:array();

      if($model_class_expose_fields ||
      (!$isset_model_class_expose_fields && $model_class_expose) ||
      (!$isset_model_class_expose_fields && !$isset_model_class_expose && $model_expose_fields) ||
      (!$isset_model_class_expose_fields && !$isset_model_class_expose && !$isset_model_expose_fields && $model_expose) ||
      (!$isset_model_class_expose_fields && !$isset_model_class_expose && !$isset_model_expose_fields && !$isset_model_expose && $global_expose)
      )
      {
        foreach($result as $key => $value)
        {
          if(in_array(strtolower($key), $fields))
          {
            unset($result[$key]);
          }  
        }
      }
      else
      {
        foreach($result as $key => $value)
        {
          if(!in_array(strtolower($key), $fields))
          {
            unset($result[$key]);
          }  
        }
      }
    }
    
    return $result;
  }
}