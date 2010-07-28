<?php
class sfHarmonyActionService extends sfHarmonyAsyncService
{
  protected $server;
  
  protected function initialize()
  {
    $this->parser_class = 'sfHarmonyWebParser';
  }

  public function go($route)
  {
    $this->fakeServer($route);

    $configuration = sfContext::getInstance()->getConfiguration();
    $configuration->getEventDispatcher()->connect('request.filter_parameters', array($this, 'filterRequest'));

    //TODO bof bof ne permet pas d'etendre le controller ! utile de pouvoir l'etendre ?
    //pour thrower les exceptions
    $controller = sfConfig::get('sf_factory_controller', 'sfFrontWebController');
    sfConfig::set('sf_factory_controller', 'sfHarmonyWebController');

    $context = sfContext::createInstance($configuration, 'harmony_'.time());

    sfConfig::set('sf_factory_controller', $controller);

    try
    {
      $context->dispatch();
    }
    catch(Exception $e)
    {
      $this->send(array('error' => $e));
    }
  }

  public function filterRequest(sfEvent $event, $values)
  {
    $values['harmony_call'] = true;
    $values['harmony_service'] = $this;

    return $values;
  }

  public function exec()
  {
    $query = $this->getParser()->parse($this->getOperation());
    $this->go(array_shift($query[0]['arguments']));
  }

  public function send($parameters)
  {
    $this->restoreServer();
    
    sfContext::switchTo(sfContext::getInstance()->getConfiguration()->getApplication());

    if(isset($parameters['caller']))
    {
      $data = array();
      foreach($parameters['caller']->getAll() as $name => $var)
      {
        $data[$name] = $var;
      }
    }
    else
    {
      $data = $parameters;
    }

    parent::send($data);
  }

  protected function fakeServer($route)
  {
    $this->server = $_SERVER;
    
    $script = $_SERVER['SCRIPT_NAME'];

    $_SERVER['REQUEST_URI'] = substr_replace($_SERVER['REQUEST_URI'], $script.$route, strpos($_SERVER['REQUEST_URI'], $script), strlen($_SERVER['REQUEST_URI']));
    
    if(isset($_SERVER['SCRIPT_URL']))
    {
      $_SERVER['SCRIPT_URL'] = $_SERVER['REQUEST_URI'];
    }
    
    if(isset($_SERVER['SCRIPT_URI']))
    {
      $_SERVER['SCRIPT_URI'] = substr_replace($_SERVER['SCRIPT_URI'], $script.$route, strpos($_SERVER['SCRIPT_URI'], $script), strlen($_SERVER['REQUEST_URI']));
    }
    
    $_SERVER['PHP_SELF'] = $_SERVER['REQUEST_URI'];
    $_SERVER['PATH_INFO'] = $route;
  }

  protected function restoreServer()
  {
    $_SERVER = $this->server;
  }
}