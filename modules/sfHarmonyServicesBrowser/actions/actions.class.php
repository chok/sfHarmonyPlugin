<?php

/**
 * serviceBrowser actions.
 *
 * @package    claymin
 * @subpackage serviceBrowser
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class sfHarmonyServicesBrowserActions extends sfActions
{
  //protected $parameters;
  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex($request)
  {
    $this->gateway = $request->getParameter('gateway');

    $this->gateways = sfHarmonyGateway::getAll();

    $this->form = new ServiceCallForm();

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('call'));

      if($this->form->isValid())
      {
        $this->getUser()->setAttribute('call_form', $this->form);

        $gateway = sfHarmonyGateway::create($this->gateway, false);
        $gateway->setCompleteCallback(array($this, 'complete'));

        switch($gateway->getType())
        {
          case 'amf':
            $requests = array($this->getAmfRequest($this->form->getValue('call')));
            $gateway->exec($requests, array());
            break;
          default:
            $gateway->exec(array($this->form->getValue('call')));
        }
      }
    }
  }

  public function complete()
  {
    $gateway = sfHarmonyGateway::getInstance();

    $response = $gateway->getResponse();
    
    $parameters = array();
    if($response instanceof SabreAMF_Message)
    {
      $parameters = $this->formatAmfResponse($response);
    }
    else
    {
      $parameters['result'] = $response;
    }

    $parameters['gateway'] = $gateway->getType();
    $parameters['form'] = $this->getUser()->getAttribute('call_form');;

    $this->getUser()->getAttributeHolder()->remove('call_form');

    //$this->context->getResponse()->send();

    $this->parameters = $parameters;
    //$this->response->send();
  }

  protected function formatAmfResponse($response)
  {
    //test de/serialization
    $output_stream = new SabreAMF_OutputStream();
    $response->serialize($output_stream);

    $input_stream = new SabreAMF_InputStream($output_stream->getRawData());

    $response = new SabreAMF_Message();
    $response->deserialize($input_stream);


    $bodies = $response->getBodies();
    $data = $bodies[0]['data'];
    $parameters = array('response' => $response);

    if($data instanceof SabreAMF_AMF3_ErrorMessage)
    {
      //erreur
      $parameters['error'] = $data->faultString;
    }
    elseif($data instanceof SabreAMF_AMF3_AcknowledgeMessage)
    {
      //result
      $parameters['result'] = $data->body;
    }

    return $parameters;
  }

  protected function getAmfRequest($parameters)
  {
    $data = new SabreAMF_AMF3_RemotingMessage();

    $data->operation = $parameters['operation'];
    $data->source = $parameters['source'];
    $data->body = $parameters['arguments'];

    $request = array(
      'target' => null,
      'response' => '/2',
      'length' => 239,
      'data' => $data
    );

    return $request;
  }

  protected function getHttpRequest($parameters)
  {

  }
}
