<?php
if (!class_exists("PlugView")) {
	exit;
}
?>
<h2><?php echo $plugin['name'] ?></h2>

<h3><?php echo _DELETE_CONFIRM?></h3>

<p><?php echo _CONFIRMTXT_TEMPLATE?><b><?php echo $template['name'] ?></b></p>

<form method="post" action="<?php echo $plugin['url'] ?>index.php"><div>
	<input type="hidden" name="action" value="templatedeleteconfirm" />
	<input type="hidden" name="tid" value="<?php echo $template['id'] ?>" />
	<input type="submit" value="<?php echo _DELETE_CONFIRM_BTN?>" />
</div></form>

