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
  <li><a href="<?php echo $plugin['url'] ?>index.php?action=templateoverview"><?php echo  _TEMPLATE_EDIT_TITLE ?></a></li>
  <li><?php echo __NP_RANK_EDIT_TITLE ?></li>
  <li><a href="index.php?action=pluginoptions&amp;plugid=<?php echo $plugin['id'] ?>"><?php echo __NP_PLUGIN_OPTION ?></a></li>
  <li><a href="<?php echo $plugin['helpurl'] ?>"><?php echo _LIST_PLUGS_HELP ?></a></p></li>
</ul>

<h2><?php echo __NP_RANK_EDIT_TITLE ?></h2>

<p>
	<strong><a href="<?php echo $plugin['url'] ?>index.php?action=blogrankedit"><?php echo __NP_RANK_EDIT_TITLE ?></a> &raquo;
	<img src='images/globe.gif' width='13' height='13' alt='' /> <?php echo htmlspecialchars($blogname) ?></strong>
</p>

<h3><?php echo __NP_CATRANK_EDIT_TITLE . $popup['rank'] ?></h3>


<form method="post" action="<?php echo $plugin['url'] ?>index.php"><div>
	<input name="action" value="rankupdate" type="hidden" />
	<input name="context" value="category" type="hidden" />
	<input name="blogid" value="<?php echo $blogid ?>" type="hidden" />
	<input name="from" value="management" type="hidden" />
	
	<table>
	<thead>
		<tr><th><?php echo __NP_RANK_ID ?></th><th><?php echo __NP_RANK_CATNAME ?></th><th><?php echo __NP_RANK_RANK ?></th></tr>
	</thead>
	<tbody>

	<?php foreach ($rank as $r): ?>

		<tr>
			<td><?php echo $r['id'] ?></td>
			<td><?php echo $r['name'] ?></td>
			<td>
				<select name="rank[<?php echo $r[id] ?>]">
			<?php
				for ($i=1; $i<=$maxrank; $i++) {
					$selected = ($r['rank'] == $i) ? ' selected="selected"' : '';
					echo '<option' . $selected . '>' . $i . '</option>';
				}
				$selected = (1000 == $r['rank']) ? ' selected="selected"' : '';
				echo '<option value="1000"' . $selected . '>'.__NP_RANK_HIDDEN.'</option>'."\n";
			?>
				</select>
				<input type="checkbox" name="rankhidden[<?php echo $r[id] ?>]" value="1" id="npbm_rankhidden" /><label for="npbm_rankhidden"><?php echo __NP_RANK_HIDDEN_TITLE ?></label>
			</td>
		</tr>

	<?php endforeach; ?>

		<tr>
			<td colspan="4">
				<input type="submit" value="<?php echo _SETTINGS_UPDATE_BTN ?>" onclick="return checkSubmit();" />
				<input type="reset" value="<?php echo __NP_SETTINGS_RESET_BTN?>" />
			</td>
		</tr>
	</tbody>
	</table>
</div></form>
