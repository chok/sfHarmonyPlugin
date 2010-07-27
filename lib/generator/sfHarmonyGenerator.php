<?php
/**
 * @author Maxime Picaud
 */
class sfHarmonyGenerator
{
  protected $parts;
  protected $template;
  protected $content;
  protected $tokens;
  protected $delimiter;
  protected $depency_pattern;

  protected $created = false;

  public function __construct($template, array $tokens, $content = null, $delimiter = null, $depency_pattern = null)
  {
    $this->template = $template;
    $this->tokens = $tokens;
    $this->content = $content;

    $this->delimiter = is_null($delimiter)?'%':$delimiter;
    $this->depency_pattern = is_null($depency_pattern)?$this->delimiter.$this->delimiter.'(depency.*?)'.$this->delimiter.$this->delimiter:$depency_pattern;
  }

  public function create($template, $tokens, $content = null, $delimiter = null, $depency_pattern = null)
  {
    return new self($template,
                    $tokens,
                    $content,
                    is_null($delimiter)?$this->delimiter:$delimiter,
                    is_null($depency_pattern)?$this->depency_pattern:$depency_pattern
                   );
  }

  public function getContent($refresh = false)
  {
    if(!$this->created)
    {
      $this->generate();
      $this->created = true;
    }

    return $this->content;
  }

  public function getTemplate()
  {
    return $this->template;
  }

  public function setTemplate(string $template)
  {
    $this->template = $template;
  }

  public function getTokens()
  {
    return $this->tokens;
  }

  public function setTokens(array $tokens)
  {
    $this->tokens = $tokens;
  }

  public function addToken($name, $token)
  {
    $this->tokens[$name] = $token;
  }

  public function addTokens(array $tokens)
  {
    $this->tokens = array_merge($this->tokens, $tokens);
  }

  public function removeToken($name)
  {
    if(isset($this->tokens[$name]))
    {
      unset($this->tokens[$name]);
    }
  }

  public function removeTokens(array $tokens)
  {
    foreach($tokens as $name => $token)
    {
      $this->removeToken($name);
    }
  }

  protected function generate()
  {
    $this->replaceTokens();

    $this->generateDepencies();
  }

  protected function generateDepencies()
  {
    $depencies = $this->getDepencies();
    foreach($depencies as $depency)
    {
      $generator = $depency->getGenerator();
      $this->mergeContent($depency, $generator->getContent());
    }
  }

  protected function getDepencies()
  {
    $depencies = array();
    if(preg_match_all('/'.$this->depency_pattern.'/', $this->template, $matches))
    {
      foreach($matches[0] as $key => $match)
      {
        $depencies[] = new sfHarmonyDepency($match, $this);
      }
    }

    return $depencies;
  }

  protected function findDepency($depency, $content = null)
  {
    $content = is_null($content)?$this->template:$content;
    return strpos($content, $depency->getField());
  }

  protected function replaceTokens()
  {
    $this->content = empty($this->content)?$this->template:$this->content;
    foreach($this->tokens as $name => $token)
    {
      $this->content = str_replace($this->delimiter.$name.$this->delimiter, $token, $this->content);
    }
  }

  protected function mergeContent($depency, $content)
  {
    if($this->findDepency($depency, $this->content) === false)
    {
      $pos = $this->findDepency($depency);
      $this->content = substr($this->content, 0 , $pos).$content.substr($this->content, $pos+1, strlen($this->content));
      var_dump($this->content);
    }
    else
    {
      $this->content = str_replace($depency->getField(), $content, $this->content);
    }
  }

  public function getDepencyTokens(sfHarmonyDepency $depency)
  {
    $name = $depency->getAttribute('name');
    if($name && isset($this->tokens['depencies']) && isset($this->tokens['depencies'][$name]))
    {
      return $this->tokens['depencies'][$name];
    }
    else
    {
      return array();
    }
  }
}