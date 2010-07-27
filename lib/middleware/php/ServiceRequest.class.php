<?php
class ServiceRequest
{
  protected $data = null;
  protected $base_url = null;
  protected $url = null;
  protected $method = null;
  protected $headers = null;
  protected $result = null;
  protected $need_calls = false;
  protected $key = null;

  public function __construct($base_url = null, $url = null, $key = null, $method = 'POST', $data = array())
  {
    $this->data = $data;

    $this->base_url = is_null($base_url)?sfConfig::get('app_am_web_service_base'):$base_url;

    $this->url = is_null($url)?sfConfig::get('app_am_web_service_url'):$url;

    $this->key = is_null($key)?sfConfig::get('app_am_web_service_key'):$key;

    $this->method = $method;

    $this->headers = array('Content-type' => 'application/x-www-form-urlencoded');
  }

  public function addObjectToRequest($model, $pk, $fields = array(),$constraints = array(), $limit = null, $order = null )
  {
    //Il faudrait gérer le cas ou on veut moins de fields pour un objet en particulier
    $this->add(array('Model' => array($model => array(
                                                      'pk' => $pk,
                                                      'fields' => $fields,
                                                      'constraints' => $constraints,
                                                    )
                                     )
                   )
             );

    if (!is_null($limit))
    {
      $this->data['Model'][$model]['limit'] = $limit;
    }

    if (!is_null($order))
    {
      $this->data['Model'][$model]['order'] = $order;
    }
  }

  public function add($data)
  {
    $this->need_calls = true;
    $this->data = array_merge_recursive($this->data, $data);
  }

  public function setData($data)
  {
    $this->data = $data;
  }

  public function getData()
  {
    return $this->data;
  }


  public function setUrl($url)
  {
    $this->url;
  }

  public function getUrl()
  {
    return $this->url;
  }

  public function setBaseUrl($base_url)
  {
    $this->base_url = $base_url;
  }


  public function getBaseUrl()
  {
    return $this->base_url;
  }

  public function getFullUrl()
  {
    $url = $this->base_url.$this->url;
    if ($this->key)
    {
      $url .= '/'.sha1($this->key.$this->getContent().$this->key);
    }

    return $url;
  }

  public function getHeaders()
  {
    $headers = array();

    foreach($this->headers as $param => $value)
    {
      $headers[] = $param.': '.$value;
    }

    return implode(' \r\n ',$headers);
  }


  protected function getContent()
  {
    return http_build_query($this->data, '', '&');
  }

 
  public function execute()
  {
    $content = $this->getContent();
    //$this->headers['Content-Length'] = strlen($content);

    $options = array(
                  'http'=>array(
                      'method' => 'POST',
                      'header' => $this->getHeaders(),//'Content-type: application/x-www-form-urlencoded',
                      'content' => $content,
                               )
                    );
    $context = stream_context_create($options);

    $this->result = json_decode(file_get_contents($this->getFullUrl(), false, $context));

    $this->need_calls = false;

    return $this->result;
  }

  public function getObject($model, $pk)
  {
    if(is_null($this->result) || $this->need_calls)
    {
      $this->execute();
    }

    if (isset($this->result->Model) &&
        isset($this->result->Model->$model) &&
        isset($this->result->Model->$model->$pk)
       )
    {
      return new RemoteObject($this->result->Model->$model->$pk);
    }
    else
    {
      return null;
    }
  }

  public function getAllObjects($model)
  {
    if(is_null($this->result) || $this->need_calls)
    {
      $this->execute();
    }

    if (isset($this->result->Model) &&
        isset($this->result->Model->$model)
       )
    {
      $array = $this->result->Model->$model;

      return self::arrayToRemoteObjects($array);
    }
    else
    {
      return array();
    }
  }

  public static function arrayToRemoteObjects($array)
  {
    $results = array();
    foreach($array as $pk => $object)
    {
      $results[$pk] = new RemoteObject($object);
    }

    return $results;
  }

  public function get($service, $default = null)
  {
    if(is_null($this->result) || $this->need_calls)
    {
      $this->execute();

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