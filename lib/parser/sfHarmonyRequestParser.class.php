<?php
class sfHarmonyRequestParser extends sfHarmonyParser
{
  public function parse($request = null)
  {
    if(is_null($request))
    {
      $request = $this->data;
    }

    
    $service = '';
    $operation = '';
    
    if(strpos($request, '|') > 0)
    {
      list($service, $operation) = explode('|', $request);
     
    }
    else
    {
      $operation = $request;  
    }
    
    return array(
                  'service'    => $service,
                  'operation' => $operation,
                );  
  }
}