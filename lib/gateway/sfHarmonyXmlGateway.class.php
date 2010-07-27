<?php
class sfHarmonyXmlGateway extends sfHarmonyGateway
{
  protected $server;

  protected function initialize($exec = true)
  {
    $this->setDispatcher('sfHarmonyServiceDispatcher');
    $this->setType('xml');
  }

  public function exec($requests = null, $header_requests = null)
  {
    foreach($requests as $id => $request)
    {
      $this->dispatch($request['source'], $request['operation'], $request['arguments'], $id);
    }
  }

  public function addResult($request_id, $data)
  {
    //TODO gerer les requests id
    $formatter = new sfHarmonyData($data);
    $this->response = '<xml>'.json_encode($formatter->getRawValue()).'</xml>';

    $this->callCompleteCallback();
  }

  public static function complete($parameters = array())
  {
    $response = sfContext::getInstance()->getResponse();
    $response->setContentType('application/json');
    $response->setContent($this->response);

    $response->send();
  }
}