<?php
class sfHarmonyFileGenerator extends sfHarmonyGenerator
{
  protected $template_path;
  protected $content_path;

  public function __construct($template_path, array $tokens, $content_path = null, $delimiter = null, $depency_pattern = null)
  {
    $template = file_get_contents(sfHarmonyFile::getFullPath($template_path));

    $content_path = sfHarmonyFile::getFullPath($content_path);
    if(file_exists($content_path))
    {
      $content = file_get_contents($content_path);
    }

    parent::__construct($template, $tokens, $content, $delimiter, $depency_pattern);

    $this->template_path = $template_path;
    $this->content_path = $content_path;
  }

  public function save($path = null)
  {
    $full_path = null;
    if(is_null($path))
    {
      $full_path = $this->content_path;
    }
    else
    {
      $full_path = sfHarmonyFile::getFullPath($path);
    }


    file_put_contents($full_path, $this->getContent());
  }
}