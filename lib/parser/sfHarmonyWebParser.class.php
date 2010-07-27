<?php
class sfHarmonyWebParser extends sfHarmonyParser
{
  public function parse($request = null)
  {
    if(is_null($request))
    {
      $request = $this->data;
    }
    
    $parts = explode('->', $request);
    
    $stack = array();
    foreach($parts as $part)
    {
      $static_parts = explode('::', $part);
      foreach($static_parts as $i => $static_part)
      {
        $static = true;
        
        if($i == 0)
        {
          $static = false;
        }
        
        $arguments = array();
        
        if(preg_match('/^(.*?)\((.*?)\)/', $static_part, $matches))
        {
            $static_part = $matches[1];
            $arguments = explode(',', $matches[2]);
            
        }
        
        $stack[] = array('static' => $static, 'operation' => $static_part, 'arguments' => $arguments);
      }
    }
    
    return $stack;
  }
}