<?php
class sfHarmonyService
{
  protected $async = false;
  protected $source;
  protected $operation;
  protected $arguments;

  public function __construct($source, $operation, $arguments = array())
  {
    $this->async = false;
    $this->source = $source;
    $this->operation = $operation;
    $this->arguments = $arguments;
  }

  public function isAsync()
  {
    return $this->async;
  }

  public function getResult()
  {

  }

  public function getClass()
  {

  }

  public function exec()
  {
    $service_class_path = str_replace(".", "/", $this->source);
    sfContext::getInstance()->getLogger()->info('{sfHarmonyPlugin} Call -> Service : '.$this->source.' | method : '.$this->operation.' | '.var_export($this->arguments, true));

    $lib_dirs = self::getProjectLibDirectories();

    $service_path = null;
    foreach ($lib_dirs as $lib_dir)
    {
      $lib_dir = $lib_dir.DIRECTORY_SEPARATOR.'services'.DIRECTORY_SEPARATOR;

      if(file_exists($lib_dir.$service_class_path.'.class.php'))
      {
        $service_path = $lib_dir.$service_class_path.'.class.php';
        break;
      }
      else if(file_exists($lib_dir.$service_class_path.'.php'))
      {
        $service_path = $lib_dir.$service_class_path.'.php';
        break;
      }
    }

    /*if (null == $service_path)
     {
     sfContext::getInstance()->getLogger()->alert('{sfXPlatformPlugin} -- Service file for '.$service_name.' does not exist in any lib-folder');
     throw new Exception('Service file for '.$service_name.' does not exist in any lib-folder');
     }*/

    if(!is_null($service_path))
    {
      require_once ($service_path);
    }

    sfContext::getInstance()->getLogger()->info('{sfHarmonyPlugin} Call -> Service : '.$service_path);

    $serviceParts = explode(".", $this->source);
    $class_name = array_pop($serviceParts);

    if(!class_exists($class_name))
    {
      sfContext::getInstance()->getLogger()->info('{sfHarmonyPlugin} Class for Service '.$this->source.' does not exist');
      throw new Exception('Class for Service Pouet class : '.$class_name.$this->source.' does not exist');
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
    $data = new sfHarmonyData($result);
    sfContext::getInstance()->getLogger()->info('{sfHarmonyPlugin} Data '.print_r($data->getRawValue(), true));

    return $data->getRawValue();
  }

  /**
   * Returns all relevant library directories of the current gateway module
   *
   * - The lib folder of th current module
   * - The application lib folder
   * - The project lib folder
   * - All lib folders of the installed plugins
   *
   * @return array The array with absolute paths of all lib-folders
   */
  protected static function getProjectLibDirectories()
  {
    // get the application lib directories
    $lib_dirs = sfContext::getInstance()->getConfiguration()->getLibDirs(sfConfig::get('sf_app'));

    // get the cross application lib dir (i.e. apps/frontend/lib)
    $lib_dirs[] = sfConfig::get('sf_app_dir').DIRECTORY_SEPARATOR.'lib';

    // get the project lib dir
    $lib_dirs[] = sfConfig::get('sf_lib_dir');

    // get the plugin lib dirs
    $lib_dirs = array_merge($lib_dirs,
    sfContext::getInstance()->
    getConfiguration()->
    getPluginSubPaths('/lib'));

    return $lib_dirs;
  }
}