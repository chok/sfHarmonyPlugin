<?php
class ServiceRequest
{
  protected $queries = null;
  protected $base_url = null;
  protected $url = null;
  protected $method = null;
  protected $headers = null;
  protected $result = null;
  protected $called = false;
  protected $key = null;

  public function __construct($queries = array(), $base_url = null, $url = null, $key = null, $method = 'POST', $headers = array())
  {
    $this->set('base_url', $base_url, 'app_service_base_url');
    $this->set('url', $url, 'app_service_url');
    $this->set('key', $key, 'app_service_key');
    $this->set('method', $method, 'app_service_method');
    
    $this->setQueries($queries);

    $this->addHeader('Content-type', 'application/x-www-form-urlencoded');
    $this->addHeaders($headers);
  }
  
  protected function set($property, $value, $config_key)
  {
    $method = 'set'.sfInflector::camelize($property);
    
    if(method_exists($this, $method))
    {
      $value = is_null($value)?sfConfig::get($config_key):$value;
      $this->$method($value);
    }
  }
  
  public function setBaseUrl($base_url)
  {
    $this->base_url = $base_url;
  }


  public function getBaseUrl()
  {
    return $this->base_url;
  }
  
  public function setUrl($url)
  {
    $this->url = $url;
  }

  public function getUrl()
  {
    return $this->url;
  }
  
  public function setKey($key)
  {
    $this->key = $key;
  }

  public function getKey()
  {
    return $this->key;
  }
  
  public function setMethod($method)
  {
    $this->method = $method;
  }

  public function getMethod()
  {
    return $this->method;
  }

  public function getFullUrl()
  {
    $url = $this->secure($this->base_url.$this->url);
    
    
    if ($this->key)
    {
      $url .= '/'.sha1($this->key.$this->getContent().$this->key);
    }

    return $url;
  }
  
  protected function secure($url)
  {
    if($this->getKey())
    {
      $url .= '/'.sha1($this->getKey().$this->getContent().$this->getKey());
    }
    
    return $url;
  }
  
  public function setHeader($key, $value)
  {
    $this->headers[$key] = $value;
  }
  
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  
  public function addHeaders($headers)
  {
    $this->setHeaders(array_merge_recursive($this->getHeaders(), $headers));
  }
  
  public function addHeader($key, $value)
  {
    $this->headers[$key] = $value;
  }
  
  public function getHeader($key, $default = null)
  {
    return isset($this->headers[$key])?$this->headers[$key]:$default;
  }
  
  public function getHeaders()
  {
    return $this->headers;
  }

  protected function headersToString()
  {
    $headers = array();

    foreach($this->headers as $param => $value)
    {
      $headers[] = $param.': '.$value;
    }

    return implode(' \r\n ',$headers);
  }
  
  public function addQuery($query)
  {
    $this->queries[] = $query;
  }
  
  public function setQueries($queries)
  {
    $this->queries = $queries;
  }

  public function getQueries()
  {
    return $this->queries;
  }

  protected function getRequest()
  {
    return http_build_query($this->getQueries(), '', '&');
  }

 
  public function send()
  {
    $request = $this->getRequest();
    //$this->headers['Content-Length'] = strlen($content);

    $options = array(
                  'http'=>array(
                      'method' => 'POST',
                      'header' => $this->headersToString(),//'Content-type: application/x-www-form-urlencoded',
                      'content' => $request,
                               )
                    );
    $context = stream_context_create($options);
    
    $this->result = file_get_contents($this->getFullUrl(), false, $context);
    
    $this->called = true;

    return $this->result;
  }

  public function get($service, $default = null)
  {
    if(is_null($this->result) || !$this->called)
    {
      $this->send();

      if (isset($this->result->$service))
      {
        foreach($this->result->$service as $key => $value)
        {
          if (is_object($value))
          {
            $this->result->$service->$key = new RemoteObject($value);
          }
        }
      }
    }

    return isset($this->result->$service)?$this->result->$service:$default;
  }
}