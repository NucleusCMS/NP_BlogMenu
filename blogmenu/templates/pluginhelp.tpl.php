<?php
if (!class_exists("PlugView")) {
	exit;
}
?>
<h2><?php echo $plugin['name'] ?></h2>

<ul>
  <li><a href="<?php echo $plugin['url'] ?>"><?php echo __NP_GENERAL_SETTINGS_TITLE ?></a></li>
  <li><a href="<?php echo $plugin['url'] ?>index.php?action=templateoverview"><?php echo  _TEMPLATE_EDIT_TITLE ?></a></li>
  <li><a href="<?php echo $plugin['url'] ?>index.php?action=blogrankedit"><?php echo __NP_RANK_EDIT_TITLE ?></a></li>
  <li><a href="index.php?action=pluginoptions&amp;plugid=<?php echo $plugin['id'] ?>"><?php echo __NP_PLUGIN_OPTION ?></a></li>
  <li><?php echo _LIST_PLUGS_HELP ?></p></li>
</ul>

<h2>NP_BlogMenu Help</h2>

<?php include_once($helpfile); ?>