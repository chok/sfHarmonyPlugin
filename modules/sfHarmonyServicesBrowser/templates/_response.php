<?php if(isset($error)): ?>
  <p>There is an error with fault : <?php echo $error ?></p>
<?php else: ?>
  <h2>Résultat : </h2>
  <?php if($result instanceof sfOutputEscaper): ?>
    <?php $result = $result->getRawValue() ?>
  <?php endif;?>
  <pre>
    <?php print_r($result) ?>
  </pre>
<?php endif;?>