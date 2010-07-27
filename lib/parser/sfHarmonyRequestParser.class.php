<?php
class sfHarmonyRequestParser extends sfHarmonyParser
{
  public function parse($request = null)
  {
    if(is_null($request))
    {
      $request = $this->data;
    }

    
    list($service, $operation) = explode('|', $request);
    
    if(!empty($service) && !empty($operation))
    {
      return array(
                    'service'    => $service,
                    'operation' => $operation,
                  );
    }
    else return null;
  }
}