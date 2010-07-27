<?php
class sfMxmlcCompiler extends sfHarmonyCompiler
{
  public function __construct($options = array())
  {
    parent::__construct('mxmlc',$options);
  }

  protected function setDefaultOptions()
  {
    $this->options = array
    (
    );
  }

  protected function setDefaultArguments()
  {
    $this->arguments = array
    (
      '/media/sda2/Documents/work/X-platform/symfony/lib/flex/src/cross.mxml'
      );
  }
}