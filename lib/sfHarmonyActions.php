<?php
abstract class sfHarmonyActions extends sfActions
{
  public function execute($request)
  {
    //non flex execute l'action normalement
    $flex = $request->getParameter('harmony_call');

    $view = parent::execute($request);


    //flex
    //redirect to module flex to insert SWFAddress
    //verifier s présence de Flash sur le client?
    if($flex)
    {
      $view = sfYaml::load(sfContext::getInstance()->getModuleDirectory().'/config/view.yml');

      if(is_array($view))
      {
        sfConfig::add($view);
      }

      $request->getParameter('harmony_service')->send(array
                                  (
                                    'caller' => $this->getVarHolder(),
                                    'view'   => $view
                                  ));

      $this->getController()->setRenderMode(sfView::RENDER_VAR);
      return sfView::NONE;
    }
    else
    {
      return $view;
    }
  }
}