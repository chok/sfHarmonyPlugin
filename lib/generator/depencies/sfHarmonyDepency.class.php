<?php
class sfHarmonyDepency
{
  protected $field;
  protected $template;
  protected $tokens;
  protected $attributes;
  protected $generator;
  protected $parent_generator;

  public function __construct($field, sfHarmonyGenerator $parent_generator)
  {
    $this->field = $field;
    $this->parent_generator = $parent_generator;
    $this->generateAttributes();
  }

  public function getGenerator()
  {
    if(is_null($this->generator))
    {
      $this->generator = $this->parent_generator->create($this->getTemplate(), $this->getTokens());
    }

    return $this->generator;
  }

  public function getAttributes()
  {
    return $this->attributes;
  }

  public function getAttribute($name)
  {
    return isset($this->attributes[$name])?$this->attributes[$name]:null;
  }

  public function getTemplate()
  {
    if(is_null($this->template))
    {
      $this->template = file_get_contents(sfHarmonyFile::getFullPath($this->getAttribute('template')));
    }

    return $this->template;
  }

  protected function generateAttributes()
  {
    if(preg_match_all('/ (.*?)="(.*?)"/', $this->field, $matches))
    {
      foreach($matches[0] as $key => $match)
      {
        $this->attributes[$matches[1][$key]] = $matches[2][$key];
      }
    }
  }

  public function getTokens()
  {
    if(is_null($this->tokens))
    {
      $this->tokens = $this->parent_generator->getDepencyTokens($this);
    }

    return $this->tokens;
  }

  public function getField()
  {
    return $this->field;
  }

  public function setField($field)
  {
    $this->field = $field;
  }
}