<?php
class sfAntCompiler extends sfHarmonyCompiler
{
  public function __construct($arguments = array(), $options = array(), $logger = null)
  {
    parent::__construct('ant',$arguments, $options, $logger);
  }

  protected function setDefaultOptions()
  {
    $plugin_path = sfConfig::get('sf_plugins_dir').'/sfHarmonyPlugin';
    $default_flex_sdk_path = $plugin_path.'/lib/vendor/flex/sdks';
    $flex_sdk_path = sfConfig::get('app_harmony_flex_sdk_path',$default_flex_sdk_path);

    $this->options = array
    (
      //TODO prendre en compte le app.yml
      'lib' => $flex_sdk_path.DIRECTORY_SEPARATOR.sfConfig::get('app_harmony_flex_sdk','3.4.1').'/ant/lib/flexTasks.jar',
      'buildfile' => $plugin_path.'/data/compiler/build.xml',
    );
  }

  protected function setDefaultArguments()
  {
    $this->arguments = array
    (
      'app' => 'cross'
    );
  }
}