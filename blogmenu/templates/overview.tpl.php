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
  <li><?php echo __NP_GENERAL_SETTINGS_TITLE ?></li>
  <li><a href="<?php echo $plugin['url'] ?>index.php?action=templateoverview"><?php echo  _TEMPLATE_EDIT_TITLE ?></a></li>
  <li><a href="<?php echo $plugin['url'] ?>index.php?action=blogrankedit"><?php echo __NP_RANK_EDIT_TITLE ?></a></li>
  <li><a href="index.php?action=pluginoptions&amp;plugid=<?php echo $plugin['id'] ?>"><?php echo __NP_PLUGIN_OPTION ?></a></li>
  <li><a href="<?php echo $plugin['helpurl'] ?>"><?php echo _LIST_PLUGS_HELP ?></a></p></li>
</ul>

<h2><?php echo __NP_GENERAL_SETTINGS_TITLE ?></h2>

<h3><?php echo __NP_MODULE_TITLE  . $popup['module'] ?></h3>
<table>
  <tr>
    <th><?php echo _LISTS_NAME ?></th>
    <th><?php echo __NP_MODULE_STATE ?></th>
    <th><?php echo _LISTS_ACTIONS ?></th>
  </tr>
  <?php foreach($modules as $module): ?>
  <tr>
    <td><?php echo $module['name'] ?></td>
    <td><?php echo ($module['enable']) ? __NP_MODULE_ENABLE : '<strong>'.__NP_MODULE_DISABLE.'</strong>' ?></td>
    <td><a href="<?php echo $plugin['url'] ?>index.php?action=moduleswitch&amp;name=<?php echo $module['name'] ?>"><?php echo __NP_MODULE_SWITCH ?></a></td>
  </tr>
  <?php endforeach; ?>
</table>


<h3><?php echo __NP_RANK_BASIC_TITLE . $popup['rankbasic'] ?></h3>

<form method="post" action="<?php echo $plugin['url'] ?>index.php"><div>
  <input name="action" value="rankbasicupdate" type="hidden" />

<table>
  <tbody>
	<tr>
	  <th colspan="2"><?php echo __NP_BLOG_TITLE ?></th>
	</tr>
	<tr>
	  <td><?php echo __NP_RANK_DEFAULT_TITLE ?></td>
	  <td>
		<select name="blogdef">
		<?php
		   for ($i=1; $i<=$rank_cnf['blogmax']; $i++) {
			  $selected = ($i == $rank_cnf['blogdef']) ? ' selected="selected"' : '';
			  echo '<option' . $selected . '>' . $i . '</option>'."\n";
		   }
		   $selected = (1000 == $rank_cnf['blogdef']) ? ' selected="selected"' : '';
		   echo '<option value="1000"' . $selected . '>'.__NP_RANK_HIDDEN.'</option>'; 
		?>
		</select>
		<input type="checkbox" name="blogdef2hidden" value="1" id="npbm_blogdef2hidden" /><label for="npbm_blogdef2hidden"><?php echo __NP_RANK_HIDDEN_TITLE ?></label>
	  </td>
	</tr>
	<tr>
	  <td><?php echo __NP_RANK_MAX_TITLE ?></td>
	  <td>
		<input type="text" name="blogmax" value="<?php echo $rank_cnf['blogmax'] ?>" size="10" maxlength="10" />
	  </td>
	</tr>
	<tr>
	  <th colspan="2"><?php echo __NP_CATEGORY_TITLE ?></th>
	</tr>
	<tr>
	  <td><?php echo __NP_RANK_DEFAULT_TITLE ?></td>
	  <td>
		<select name="catdef">
		<?php
		   for ($i=1; $i<=$rank_cnf['catmax']; $i++) {
			  $selected = ($i == $rank_cnf['catdef']) ? ' selected="selected"' : '';
			  echo '<option' . $selected . '>' . $i . '</option>'."\n";
		   }
		   $selected = (1000 == $rank_cnf['catdef']) ? ' selected="selected"' : '';
		   echo '<option value="1000"' . $selected . '>'.__NP_RANK_HIDDEN.'</option>'; 
		?>
		</select>
		<input type="checkbox" name="catdef2hidden" value="1" id="npbm_catdef2hidden" /><label for="npbm_catdef2hidden"><?php echo __NP_RANK_HIDDEN_TITLE ?></label>
	  </td>
	</tr>
	<tr>
	  <td><?php echo __NP_RANK_MAX_TITLE ?></td>
	  <td>
		<input type="text" name="catmax" value="<?php echo $rank_cnf['catmax'] ?>" size="10" maxlength="10" />
	  </td>
	</tr>
	<tr>
	  <th colspan="2"><?php echo _SETTINGS_UPDATE ?></th>
	</tr>
	<tr>
	  <td>
		<input type="submit" value="<?php echo _SETTINGS_UPDATE_BTN ?>" />
	  </td>
	  <td>
		<input type="checkbox" name="update_existdef" value="1" id="npbm_update_existdef" /><label for="npbm_update_existdef"><?php echo __NP_RANK_UPDATE_EXIST_DEF ?></label>
	  </td>
	</tr>
  </tbody>
</table>

</div></form>
