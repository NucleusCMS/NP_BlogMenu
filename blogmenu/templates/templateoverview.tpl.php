<?php
if (!class_exists("PlugView")) {
	exit;
}
?>
<h2><?php echo $plugin['name'] ?></h2>

<?php if ($message): ?>
  <p class="batchoperations" style="text-align:left"><?php echo htmlspecialchars($message) ?></p>
<?php endif; ?>

<ul>
  <li><a href="<?php echo $plugin['url'] ?>"><?php echo __NP_GENERAL_SETTINGS_TITLE ?></a></li>
  <li><?php echo  _TEMPLATE_EDIT_TITLE ?></li>
  <li><a href="<?php echo $plugin['url'] ?>index.php?action=blogrankedit"><?php echo __NP_RANK_EDIT_TITLE ?></a></li>
  <li><a href="index.php?action=pluginoptions&amp;plugid=<?php echo $plugin['id'] ?>"><?php echo __NP_PLUGIN_OPTION ?></a></li>
  <li><a href="<?php echo $plugin['helpurl'] ?>"><?php echo _LIST_PLUGS_HELP ?></a></p></li>
</ul>

<h2><?php echo  _TEMPLATE_EDIT_TITLE ?></h2>

<h3><?php echo _TEMPLATE_TITLE ?></h3>

<table>
  <caption><?php echo _TEMPLATE_AVAILABLE_TITLE ?></caption>
  <thead>
	<tr>
	  <th><?php echo _LISTS_NAME ?></th>
	  <th><?php echo _LISTS_DESC ?></th>
	  <th colspan='3'><?php echo _LISTS_ACTIONS ?></th>
	</tr>
  </thead>
  <tbody>
	<?php foreach ($templates as $t): ?>
		<tr onmouseover='focusRow(this);' onmouseout='blurRow(this);'>
		  <td><?php echo $t['name'] ?></td>
	<td><?php echo htmlspecialchars($t['description']) ?></td>
		  <td><a href="<?php echo $plugin['url'] ?>index.php?action=templateedit&amp;tid=<?php echo $t['id'] ?>"><?php echo _LISTS_EDIT ?></a></td>
		  <td><a href="<?php echo $plugin['url'] ?>index.php?action=templateclone&amp;tid=<?php echo $t['id'] ?>"><?php echo _LISTS_CLONE ?></a></td>
		  <td><a href="<?php echo $plugin['url'] ?>index.php?action=templatedelete&amp;tid=<?php echo $t['id'] ?>"><?php echo _LISTS_DELETE ?></a></td>
		</tr>
	<?php endforeach; ?>
  </tbody>
</table>


<h3><?php echo _TEMPLATE_NEW_TITLE ?></h3>

<form method="post" action="<?php echo $plugin['url'] ?>index.php"><div>
  <input name="action" value="templatenew" type="hidden" />
  <table><tbody><tr>
	<td><?php echo _TEMPLATE_NAME . $popup['name'] ?></td>
	<td><input name="tname" size="20" maxlength="20" /></td>
  </tr><tr>
	<td><?php echo _TEMPLATE_DESC?></td>
	<td><input name="tdesc" size="60" maxlength="200" /></td>
  </tr><tr>
	<td><?php echo _TEMPLATE_CREATE?></td>
	<td><input type="submit" value="<?php echo _TEMPLATE_CREATE_BTN?>" onclick="return checkSubmit();" /></td>
  </tr></tbody></table>
</div></form>
