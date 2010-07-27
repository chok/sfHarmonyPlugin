<?php
class sfHarmonyModelGenerator extends sfHarmonyFileGenerator
{
  public function __construct($template)
  {

  }

  protected function generate()
  {
    $paths = sfFinder::type('file')->name('*.class.php')->in(sfConfig::get('sf_lib_dir').'/model');

    foreach($paths as $path)
    {
      if(preg_match('/\/([a-zA-Z0-9]*?)\.class\.php/', $path, $matches))
      {
        $class = $matches[1];
        $reflect = new ReflectionClass($class);
        if(!$reflect->isAbstract())
        {
          $this->create();
        }
      }
    }
  }

  public function save($path)
  {

  }
}