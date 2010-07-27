<?php
class sfHarmonyPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    //$this->dispatcher->connect('debug.web.load_panels', array($this, 'configureWebDebugToolbar'));
    //$this->dispatcher->connect('routing.load_configuration', array('sfHarmonyRouting', 'listenToRoutingLoadConfigurationEvent'));
  }

  public function configureWebDebugToolbar(sfEvent $event)
  {
    $web_debug_toolbar = $event->getSubject();

    $web_debug_toolbar->setPanel('harmony', new sfWebDebugPanelHarmony($web_debug_toolbar));

  }
}