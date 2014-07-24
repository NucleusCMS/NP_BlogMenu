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

<p>
	<strong>
	<a href="<?php echo $plugin['url'] ?>index.php?action=templateoverview"><?php echo  _TEMPLATE_EDIT_TITLE ?></a>
	&raquo; <?php echo $data['tname'] ?>
	</strong>
</p>

<form method="post" action="<?php echo $plugin['url'] ?>index.php">
	<div>
	<input type="hidden" name="action" value="templateupdate" />
	<input type="hidden" name="tid" value="<?php echo $data['tid'] ?>" />
	
	<table>
		<tr>
			<th colspan="2"><?php echo _TEMPLATE_SETTINGS ?></th>
		</tr><tr>
			<td><?php echo _TEMPLATE_NAME?></td>
			<td><input type="text" name="tname" size="20" maxlength="20" value="<?php echo $data['tname'] ?>" /></td>
		</tr><tr>
			<td><?php echo _TEMPLATE_DESC?></td>
			<td><input type="text" name="tdesc" size="60" maxlength="200" value="<?php echo htmlspecialchars($data['tdesc']) ?>" /></td>
		</tr><tr>
			<th colspan="2"><?php echo _TEMPLATE_UPDATE?></th>
		</tr><tr>
			<td><?php echo _TEMPLATE_UPDATE ?></td>
			<td>
				<input type="submit" value="<?php echo _TEMPLATE_UPDATE_BTN?>" onclick="return checkSubmit();" />
				<input type="reset" value="<?php echo _TEMPLATE_RESET_BTN?>" />
			</td>
	
		</tr><tr>
			<th colspan="2"><?php echo __NP_TEMPLATE_BLOGLIST . $popup['bloglist'] ?></th>
		</tr><tr>
			<td><?php echo __NP_TEMPLATE_BLOGLIST_HEADER ?></td>
	<td><textarea name="blogheader" cols="50" rows="5"><?php echo htmlspecialchars($data['blogheader']) ?></textarea></td>
		</tr><tr>
			<td><?php echo __NP_TEMPLATE_BLOGLIST_ITEM ?></td>
			<td><textarea name="bloglist" cols="50" rows="5"><?php echo htmlspecialchars($data['bloglist']) ?></textarea></td>
		</tr><tr>
			<td><?php echo __NP_TEMPLATE_BLOGLIST_FOOTER ?></td>
			<td><textarea name="blogfooter" cols="50" rows="5"><?php echo htmlspecialchars($data['blogfooter']) ?></textarea></td>
		</tr><tr>
			<td><?php echo __NP_TEMPLATE_BLOGLIST_FLAG ?></td>
			<td><textarea name="blogflag" cols="50" rows="5"><?php echo htmlspecialchars($data['blogflag']) ?></textarea></td>
		</tr><tr>
			<th colspan="2"><?php echo __NP_TEMPLATE_BLOGALIASES . $popup['aliases'] ?></th>
		</tr><tr>
			<td><?php echo __NP_TEMPLATE_BLOGALIASES ?></td>
			<td><textarea name="aliases" cols="50" rows="5"><?php echo htmlspecialchars($data['aliases']) ?></textarea></td>
		</tr><tr>
			<th colspan="2"><?php echo __NP_TEMPLATE_CATLIST . $popup['categorylist'] ?></th>
		</tr><tr>
			<td><?php echo __NP_TEMPLATE_CATLIST_HEADER ?></td>
			<td><textarea name="catheader" cols="50" rows="5"><?php echo htmlspecialchars($data['catheader']) ?></textarea></td>
		</tr><tr>
			<td><?php echo __NP_TEMPLATE_CATLIST_ITEM ?></td>
			<td><textarea name="catlist" cols="50" rows="5"><?php echo htmlspecialchars($data['catlist']) ?></textarea></td>
		</tr><tr>
			<td><?php echo __NP_TEMPLATE_CATLIST_FOOTER ?></td>
			<td><textarea name="catfooter" cols="50" rows="5"><?php echo htmlspecialchars($data['catfooter']) ?></textarea></td>
		</tr><tr>
			<td><?php echo __NP_TEMPLATE_CATLIST_FLAG ?></td>
			<td><textarea name="catflag" cols="50" rows="5"><?php echo htmlspecialchars($data['catflag']) ?></textarea></td>
	
		</tr><tr>
			<th colspan="2"><?php echo _TEMPLATE_UPDATE?></th>
		</tr><tr>
			<td><?php echo _TEMPLATE_UPDATE?></td>
			<td>
				<input type="submit" value="<?php echo _TEMPLATE_UPDATE_BTN?>" onclick="return checkSubmit();" />
				<input type="reset" value="<?php echo _TEMPLATE_RESET_BTN?>" />
			</td>
		</tr>
	</table>
		
	</div>
</form>
