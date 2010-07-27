<?php
abstract class sfHarmonyCompiler
{
  protected $filesystem;

  protected $compiler;

  protected $options;

  protected $arguments;

  protected $cmd;

  protected $logger;

  public function __construct($compiler,$arguments = array(), $options = array(), $logger = null)
  {
    $this->filesystem = null;
    $this->cmd = "";

    $this->setDefaultOptions();
    $this->addOptions($options);

    $this->setDefaultArguments();
    $this->addArguments($arguments);

    $this->setCompiler($compiler);

    $this->logger = $logger;
  }

  public function setCompiler($compiler)
  {
    $this->compiler = $compiler;
  }

  public function getCompiler()
  {
    return $this->compiler;
  }

  public function getCmd()
  {
    $this->buildCmd();

    return $this->cmd;
  }

  protected function buildCmd()
  {
    $this->cmd = $this->getCompiler().$this->getOptionsCmd().$this->getArgumentsCmd();
  }

  abstract protected function setDefaultOptions();
  abstract protected function setDefaultArguments();


  public function getOptionsCmd()
  {
    $options = "";
    foreach($this->getOptions() as $option => $value)
    {
      $options .= " -".$option." ".$value;
    }

    return $options;
  }

  public function getOption($key)
  {
    return $this->options[$key];
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function addOption($key,$value)
  {
    $this->options[$key] = $value;
  }

  public function addOptions($options)
  {
    $this->options = array_merge($this->options,$options);
  }

  public function getArgumentsCmd()
  {
    $arguments = "";
    foreach($this->getArguments() as $arg)
    {
      $arguments .= " ".$arg;
    }

    return $arguments;
  }

  public function getArguments()
  {
    return $this->arguments;
  }

  public function addArgument($arg)
  {
    $this->arguments[] = $arg;
  }

  public function addArguments($arguments)
  {
    $this->arguments = array_merge($this->arguments,$arguments);
  }

  protected function getFileSystem()
  {
    if(is_null($this->filesystem))
    {
      $this->filesystem = new sfFilesystem();
    }

    return $this->filesystem;
  }

  public function compile()
  {
    try
    {
      $out = isset($this->logger['out'])?$this->logger['out']:array($this, 'out');
      $err = isset($this->logger['err'])?$this->logger['err']:array($this, 'err');

      return $this->getFileSystem()->execute($this->getCmd(), $out, $err);
    }
    catch(sfException $e)
    {
      if(!(strpos('not found',$e->getMessage()) === false))
      {
        $e = new sfException(sprintf('Compiler "%s" was not found, please install it to proceed(eg: sudo apt-get install %s)',$this->getCompiler(),$this->getCompiler()));
      }

      throw $e;
    }
  }

  public function out($lines)
  {
    echo $lines;
  }

  public function err($lines)
  {
    echo "err\n'";var_dump($lines);
    echo strpos($lines,'not found');
    if(strpos($lines,'not found') > 0)
    {
      throw new RuntimeException($lines);
    }

  }
}