<?php
class sfHarmonyRouting
{
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $routing = $event->getSubject();
    // add plug-in routing rules on top of the existing ones
    $routing->prependRoute('gateway', new sfRoute('/gateway/:gateway', array('module' => 'sfHarmonyGateway','action' => 'gateway')));
  }
}