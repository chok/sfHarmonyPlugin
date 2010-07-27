<ul>
  <?php foreach($gateways as $gateway): ?>
    <li><?php echo link_to(sfInflector::humanize($gateway), 'sfHarmonyServicesBrowser/index?gateway='.$gateway) ?></li>
  <?php endforeach;?>
</ul>