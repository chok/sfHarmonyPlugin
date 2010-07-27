<?php
class sfWebDebugPanelHarmony extends sfWebDebugPanel
{
  public function getTitle()
  {
    return sfHarmonyGateway::getCurrent();
  }

  public function getPanelTitle()
  {
    return 'Harmony Panel';
  }

  public function getPanelContent()
  {
    return 'harmony content';
  }

}