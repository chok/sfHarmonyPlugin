<?php
class ServiceObject
{
  protected $data;

  public function __construct($data)
  {
    $data2 = new stdClass();
    foreach($data as $key => $value)
    {
      $key = strtolower($key);
      $data2->$key = $value;
    }
    
    $this->data = $data2;
  }

  public function __call($method, $arguments)
  {
    //uniquement des getters
    $underscored = sfInflector::underscore($method);
    $underscored = explode('_',$underscored);
    array_shift($underscored);
    $field = implode('_',$underscored);

    if (isset($this->data->$field))
    {
      if(is_object($this->data->$field))
      {
        return new ServiceObject($this->data->$field);
      }
      else
      {
        return $this->data->$field;
      }
    }
    else
    {
      return null;
      //throw new sfException(sprintf('La méthode %s n\'existe pas',$method));
    }
  }
  
  public function __toString()
  {
    $string = array();
    
    foreach($this->data as $field => $value)
    {
      $string[] = $field.' : '.$value;
    }
    
    return implode(' | ', $string);
  }
}