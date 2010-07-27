<?php
class ServiceCallForm extends sfForm
{
  public function configure()
  {
    $this->widgetSchema['call'] = new sfWidgetFormInput(array(),array('style'=>'width:400px;'));
    //TODO By parsing the value !
    $this->validatorSchema['call'] = new sfValidatorPass();

    $this->widgetSchema->setNameFormat('call[%s]');
  }

  public function parse()
  {
    $parser = new sfHarmonyRequestParser($this->getValue('call'));
    
    return $parser->parse();
  }
}
?>