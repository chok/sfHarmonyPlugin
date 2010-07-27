<?php
class ServiceObject
{
  protected $data;

  public function __construct($data)
  {
    $this->data = $data;
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
        return new RemoteObject($this->data->$field);
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
}