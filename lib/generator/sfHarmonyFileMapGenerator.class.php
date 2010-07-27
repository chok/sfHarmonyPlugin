<?php
class sfHarmonyFileMapGenerator extends sfHarmonyGenerator
{
  protected $map;
  protected $path;
  protected $parent_path;

  public function __construct($map, $path, $parent_path)
  {
    $this->map_path = $map_path;
  }

  protected function getMap()
  {
    $path = $this->getPath($this->map_path);

    $map_path = $this->map_path.'-map.xml';
    $full_path = $path.DIRECTORY_SEPARATOR.$framework_file;

    if(file_exists($full_path))
    {
      $map = simplexml_load_file($full_path);

      return $map;
    }
    else
    {
      throw new sfException(sprintf('Framework "%s" is not implemented : map "%s" not found in "%s" not found', $framework, $framework_file, $path));
    }
  }

  protected function getPath($path, $root = null)
  {
    if($path[0] == '/')
    {
      $root = sfConfig::get('sf_root_dir');
      $path = substr($path, 1, strlen($path) - 1);
    }
    elseif(is_null($root))
    {
      $root = dirname(__FILE__);
    }

    $path = $this->replaceTokens($root.DIRECTORY_SEPARATOR.$path);

    return $path;
  }

  protected function replaceTokens($path)
  {
    if(preg_match_all('/%(.*?)%/', $path, $matches))
    {
      $replaced = array();
      foreach($matches[0] as $key => $match)
      {
        $var = $matches[1][$key];
        if(!in_array($var, $replaced))
        {
          if(isset($this->task_arguments[$var]))
          {
            $replace = $this->task_arguments[$var];
          }
          elseif(isset($this->task_options[$var]))
          {
            $replace = $this->task_arguments[$var];
          }
          else
          {
            throw new sfException(sprintf('"%s" not found', $match));
          }

          $path = str_replace($match, $replace, $path);
          $replaced[] = $var;
        }
      }
    }

    return $path;
  }

  protected function create($map, $parent_path = null)
  {
    if($path = (string)$map->attributes()->path)
    {
      $parent_path = $this->getPath($path, $parent_path);
    }
    $this->createElem($map, $parent_path);

    foreach($map->children() as $child)
    {
      $this->create($child, $parent_path);
    }
  }

  protected function createElem($elem, $path)
  {
    $method = sfInflector::camelize('create_'.$elem->getName());
    if(method_exists($this, $method))
    {
      $this->$method($elem, $path);
    }
    else
    {
      throw new sfException(sprintf('tag "%s" is not yet implemented', $elem->getName()));
    }
  }

  protected function createFolder($elem, $path)
  {
    if(!file_exists($path))
    {
      mkdir($path);
      $this->logSection('+dir', $path);
    }
  }

  protected function createFile($elem, $path)
  {
    $path = (string) $path.DIRECTORY_SEPARATOR.$elem->attributes()->name;

    if(!file_exists($path))
    {
      $class_generator = 'sf'.sfInflector::camelize('harmony_'.(string)$elem->attributes()->generator.'_generator');

      if(class_exists($class_generator))
      {
        $generator = new $class_generator('main');
      }
      else
      {
        throw new sfException(sprintf('class "%s" for %s generator not found', $class_generator, (string)$elem->attributes()->generator));
      }

      $generator->save($path);
      $this->logSection('+file', $path);
    }
  }
}