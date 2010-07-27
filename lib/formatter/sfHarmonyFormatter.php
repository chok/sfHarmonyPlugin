<?php
class sfHarmonyFormatter
{
  protected $data;

  public function __construct($data)
  {
    $this->data = $data;
  }

  public function getRawValue()
  {
    return $this->convert($this->data);
  }

  protected function convert($data)
  {
    $result = array();
    
    if($data instanceof stdClass || is_array($data) || $data instanceof ArrayAccess)
    {
      foreach($data as $key => $value)
      {
        $result[$key] = $this->convert($value);
      }
      return $result;
    }
    elseif(is_object($data) && method_exists($data, 'toArray'))
    {
      //return new SabreAMF_TypedObject('model.Post', $data->toArray());
      return $data->toArray();
    }
    else
    {
      return $data;
    }
  }


}