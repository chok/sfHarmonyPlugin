<?php

/**
 * cross actions.
 *
 * @package    XPlatform
 * @subpackage cross
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class sfHarmonyGatewayActions extends sfActions
{
  public function executeGateway(sfWebRequest $request)
  {
    $this->setLayout(false);

    $gateway = $request->getParameter('gateway');
    
    sfHarmonyGateway::create($gateway, true, $request);
    
    //$gateway = sfHarmonyGateway::create($gateway, false, $request);
    
    //$requests = array('')
    
    

    //Gateway take control on sending response
    //TODO Find a solution to eliminate sfRenderingFilter
    $this->getController()->setRenderMode(sfView::RENDER_VAR);

    return sfView::NONE;
  }
}
