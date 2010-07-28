<?php
class sfHarmonyGateway
{
  protected $dispatcher;
  protected $formatters;
  protected $result;
  protected $response;
  protected $type;
  protected $completed_callback;
  protected $request;
  
  protected $nb_requests;
  protected $nb_completed_requests;

  private static $gateways;
  private static $current;

  public function __construct($gateway = null, $exec = true, $request = null)
  {
    if(isset(self::$gateways[$gateway]))
    {
      throw new sfException('Gateway is a singleton');
    }
    else
    {
      set_error_handler(array('sfHarmonyGateway', 'errorHandler'));
      $this->setCompleteCallback(array($this, 'complete'));
      $this->setFormatters = array('doctrine', 'propel', 'base');
      $this->nb_completed_requests = 0;
      
      $this->request = $request;

      $this->initialize($exec);
    }
  }

  public function setCompleteCallback($callback)
  {
    $this->complete_callback = $callback;
  }

  public function getCompleteCallback()
  {
    return $this->complete_callback;
  }

  public function callCompleteCallback()
  {
    $this->nb_completed_requests++;
    
    if($this->getNbRequests() == $this->getNbCompletedRequests())
    {
      return call_user_func($this->complete_callback);
    }
  }

  protected function initialize()
  {
    $this->setRequestParser('sfHarmonyRequestParser');
    $this->setDispatcher('sfHarmonyServiceDispatcher');
    $this->setType('text');
  }

  public static function getInstance($gateway = null)
  {
    if(is_null($gateway))
    {
      $gateway = self::$current;
    }

    if(isset(self::$gateways[$gateway]))
    {
      return self::$gateways[$gateway];
    }
    else throw new sfException(sprintf('Gateway "%s" does not exists', $gateway));
  }

  public static function getCurrent()
  {
    return self::$current;
  }

  final public static function create($gateway_name = null, $exec = true, $request = null)
  {
    sfContext::getInstance()->getLogger()->info('{sfHarmonyPlugin} Create : '.var_export($gateway_name, true));
    if(empty($gateway_name) || $gateway_name == 'default')
    {
        $gateway = '';
    }
    else
    {
      $gateway = sfInflector::humanize($gateway_name);
    }
    
    $class_name = 'sfHarmony'.$gateway.'Gateway';

    if(class_exists($class_name))
    {
      if(empty($gateway_name))
      {
        $gateway_name = 'default';
      }
      
      self::$current = $gateway_name;
      self::$gateways[$gateway_name] = new $class_name($gateway_name, $exec, $request);
      
      if($exec)
      {
        self::$gateways[$gateway_name]->exec();
      }
      
      return self::$gateways[$gateway_name];
    }
    else
    {
      throw new sfException(sprintf('Class "%s" for "%s" Gateway does not exists',$class_name,$gateway));
    }
  }

  public static function errorHandler ($errno, $errstr, $errfile, $errline)
  {
    throw new Exception($errstr, $errno);
  }

  public function setDispatcher($dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  public function getDispatcher()
  {
    return $this->dispatcher;
  }
  
  public function getNbRequests()
  {
    return $this->nb_requests;
  }
  
  public function getNbCompletedRequests()
  {
    return $this->nb_completed_requests;
  }
  
  public function setRequestParser($request_parser)
  {
    $this->request_parser = $request_parser;
  }

  public function getRequestParser()
  {
    return $this->request_parser;
  }

  public function setFormatters(array $formatters)
  {
    $this->formatters = $formatters;
  }

  public function getFormatters()
  {
    return $this->formatters;
  }

  public function getFormatter($name)
  {
    return isset($this->formatters[$name])?$this->formatters[$name]:null;
  }

  public function setFormatter($name, $formatter)
  {
    $this->formatters[$name] = $formatter;
  }
  
  public function addResult($request_id, $data)
  {
    //TODO gerer les requests id
    $formatter = new sfHarmonyData($data);
    $this->response = print_r($formatter->getRawValue(), true);

    $this->callCompleteCallback();
  }

  public function getResult()
  {
    return $this->result;
  }

  public function setResult($result)
  {
    $this->result = $result;
  }

  public function getResponse()
  {
    return $this->response;
  }

  public function setResponse($response)
  {
    $this->response = $response;
  }

  public function send()
  {
    $this->response->send();
  }

  public static function complete()
  {
    self::$gateways[self::$current]->complete();
  }

  public function dispatch($service, $operation, $request_id = null)
  {
    if(class_exists($this->dispatcher))
    {
      $dispatcher = new $this->dispatcher($service, $operation, $request_id);
      $dispatcher->dispatch();
    }
    else throw new sfException(sprintf('Dispatcher "%s" does not exists', $this->dispatcher));
  }
  
  public function parse($request)
  {
    if(class_exists($this->request_parser))
    {
      $parser = new $this->request_parser($request);
      return $parser->parse();
    }
    else throw new sfException(sprintf('Parser "%s" does not exists', $this->request_parser));
  }
  
  public function parseAll($requests)
  {
    sfContext::getInstance()->getLogger()->log(var_export($requests, true));
    foreach($requests as $key => $request)
    {
      $requests[$key] = $this->parse($request);
      
      if(is_null($requests[$key])) unset($requests[$key]);
    }
    
    return $requests;
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  public function getType()
  {
    return $this->type;
  }
  
  public function exec($requests = null)
  {
    if(is_null($requests) || !is_array($requests) || count($requests) == 0)
    {
      $requests = $this->readRequests();
    }

    $requests = $this->parseAll($requests);
    
    $this->nb_requests = count($requests);
    
    if(count($requests) > 0)
    {
      foreach($requests as $id => $request)
      {
        $this->dispatch($request['service'], $request['operation'], $id);
      }
    }
    else throw new sfException('No request received.');
  }
  
  protected function readRequests()
  {
    $requests = $this->request->getParameterHolder()->getAll();

    unset($requests['module'], $requests['action'], $requests['gateway']);
    
    //secure
    /*$hash = null;
    if (isset($query['hash']))
    {
      $hash = $query['hash'];
      unset($query['hash']);
    }*/

    sfContext::getInstance()->getLogger()->info('{WebService Gateway} $params '.var_export($requests, true));

    /*if ($key = sfConfig::get('app_am_web_service_key'))
    {
      $real_hash = sha1($key.http_build_query($query, '', '&').$key);

      $query = $real_hash == $hash?$query:null;
    }*/

    return $requests;
  }
  
  public static function getAll()
  {
    $paths = sfFinder::type('file')->name('sfHarmony*Gateway.class.php')->in(sfConfig::get('sf_root_dir'));

    $gateways = array('default');
    foreach($paths as $path)
    {
      if(preg_match('/sfHarmony([a-zA-Z0-9]*?)Gateway\.class\.php/', $path, $matches))
      {
        $name = $matches[1];
        if(!empty($name))
        {
          $gateways[] = sfInflector::underscore($name);
        }
      }
    }

    return $gateways;
  }
}
