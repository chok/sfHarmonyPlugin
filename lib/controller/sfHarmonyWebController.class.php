<?php
class sfHarmonyWebController extends sfWebController
{
  /**
   * Dispatches a request.
   *
   * This will determine which module and action to use by request parameters specified by the user.
   */
  public function dispatch()
  {
    sfFilter::$filterCalled = array();
  
    // determine our module and action
    $request    = $this->context->getRequest();
    $moduleName = $request->getParameter('module');
    $actionName = $request->getParameter('action');
  
    if (empty($moduleName) || empty($actionName))
    {
      throw new sfError404Exception(sprintf('Empty module and/or action after parsing the URL "%s" (%s/%s).', $request->getPathInfo(), $moduleName, $actionName));
    }
  
    // make the first request
    $this->forward($moduleName, $actionName);
  }
}