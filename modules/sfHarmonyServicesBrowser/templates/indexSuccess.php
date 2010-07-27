<?php echo link_to('<h1>Services</h1>', '@harmony_services') ?>
<?php if($gateway): ?>
  <h3>View <?php echo $gateway ?> output</h3>
<?php endif; ?>
<?php include_partial('sfHarmonyServicesBrowser/call', array('gateway' => $gateway, 'form' => $form)) ?>

<?php if(isset($parameters) && is_array($parameters->getRawValue())): ?>
  <?php include_partial('sfHarmonyServicesBrowser/response', $parameters) ?>
<?php endif; ?>

<h1>Gateways</h1>
<h2>Choose one to test a service : </h2>
 <?php include_partial('gateways', array('gateways' => $gateways))?>
