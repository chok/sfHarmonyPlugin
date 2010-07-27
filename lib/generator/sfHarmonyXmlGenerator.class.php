<?php
class sfHarmonyXmlGenerator extends sfHarmonyFileGenerator
{
  protected $formatted = false;

  public function __construct($template_path, $tokens, $content_path = null, $delimiter = null, $depency_pattern = null)
  {
    if(is_null($depency_pattern))
    {
      $depency_pattern = '<template(.*?)>';
    }
    parent::__construct($template_path, $tokens, $content_path, $delimiter, $depency_pattern);
  }

  public function getDom($path)
  {
    $dom = new DomDocument();
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;

    $dom->load($path);

    return $dom;
  }

  public function getContent($refresh = false)
  {
    if(!$this->formatted)
    {
      $dom = new DomDocument();
      $dom->formatOutput = true;
      $dom->preserveWhiteSpace = false;

      $dom->loadXml(parent::getContent());

      $this->content = $dom->saveXml();
      $this->formatted = true;
    }

    return parent::getContent($refresh);
  }
}