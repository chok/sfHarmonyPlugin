<?php
class sfHarmonyFile
{
  static public function getFullPath($path, $root = null)
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

    return $root.DIRECTORY_SEPARATOR.$path;
  }
}