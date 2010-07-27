<?php
class sfHarmonyJsonGateway extends sfHarmonyGateway
{
  protected function initialize($exec = true)
  {
    $this->setRequestParser('sfHarmonyRequestParser');
    $this->setDispatcher('sfHarmonySecureServiceDispatcher');
    $this->setType('json');
  }

  public function addResult($request_id, $data)
  {
    //TODO gerer les requests id
    $formatter = new sfHarmonySecureFormatter($data);
    $this->result[] = $formatter->getRawValue();
    
    $this->response = json_encode($this->result);

    $this->callCompleteCallback();
  }

  public static function complete($parameters = array())
  {
    $response = sfContext::getInstance()->getResponse();
    $response->setContentType('application/json');
    $response->setContent(self::getInstance()->getResponse());

    $response->send();
  }
}