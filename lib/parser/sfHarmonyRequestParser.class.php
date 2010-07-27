<?php
class sfHarmonyRequestParser extends sfHarmonyParser
{
  public function parse($request = null)
  {
    if(is_null($request))
    {
      $request = $this->data;
    }

    
    try 
    {
      $parts = explode('->', $request);
    }
    catch(Exception $e)
    {
      var_dump($request);die();  
    }

    $source = null;
    $operation = null;
    $arguments = array();
    $static = false;

    if(count($parts) > 1)
    {
      $operation = array_pop($parts);
      $source = array_shift($parts);
    }
    else
    {
      $static = true;
      $parts = explode('::', $request);
      if(count($parts) == 2)
      {
        $operation = $parts[1];
        $source = $parts[0];
      }
      elseif(count($parts) > 2)
      {
        $operation = array_pop($parts);
        $source = implode('::', $parts);
      }
    }

    if($operation)
    {
      //TODO nul pas de pris en compte d'autre parentheses !!!
      $parts = explode('(', $operation);
      $operation = $parts[0];
      $parts = explode(')', $parts[1]);
      $arguments = explode(',', $parts[0]);
    }

    if(!empty($source) && !empty($operation))
    {
      return array(
                    'static'    => $static,
                    'source'    => $source,
                    'operation' => $operation,
                    'arguments' => $arguments
                  );
    }
    else return null;
  }
}