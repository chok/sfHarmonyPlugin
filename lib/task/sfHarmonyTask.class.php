<?php

abstract class sfHarmonyTask extends sfBaseTask
{
  protected function createFolder($path)
  {
    if(!file_exists($path))
    {
      mkdir($path);
      $this->logSection('+dir', $path);
    }
  }
}
