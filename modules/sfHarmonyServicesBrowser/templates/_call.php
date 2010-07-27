<?php $route = '@harmony_services'.(empty($gateway)?'':'_gateway?gateway='.$gateway) ?>
<?php echo form_tag($route) ?>
  <?php echo $form ?>
  <input type='submit'>
</form>
