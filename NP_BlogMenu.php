<?php

class NP_BlogMenu extends NucleusPlugin
{

	var $opt;
	var $templates;
	var $modules;
	var $defblugurl;
	
	function getName() { return 'Blog Menu'; }
	function getAuthor() { return 'Taka'; }
	function getURL() { return ''; }
	function getVersion() { return '0.2'; }
	function getMinNucleusVersion() { return '350'; }
	function getMinNucleusPatchLevel() { return 0; }
	function getDescription ()
	{
		return 'Bloglist, Categorylist';
	}
	
	function supportsFeature($what) {return in_array($what,array('SqlApi','SqlTablePrefix','HelpPage'));}
	
	function hasAdminArea() { return 1; }
	
	function getEventList()
	{
		 return array(
		 	'PostAddBlog', 'PostAddCategory', 'PostDeleteBlog', 
		 	'PostDeleteCategory', 'BlogSettingsFormExtras', 'QuickMenu'
		 );
	}
	
	function getTableList()
	{ 
		return array(
			sql_table('plug_blogmenu_rank'),
			sql_table('plug_blogmenu_template'),
		);
	}
	
	function install ()
	{
		global $CONF;
		
		$file = $this->getDirectory() . 'installer/'.$CONF['Language'].'.installer.php';
		if (!file_exists($file)) {
			$file = $this->getDirectory() . 'installer/english.installer.php';
		}
		require ($file);
		
		$installer = new BlogMenuInstaller;
		$installer->install($this);
	}
	
	function uninstall ()
	{
		if ($this->getOption('del_uninstall') == "yes") {
			sql_query("DROP table ".sql_table('plug_blogmenu_rank'));
			sql_query("DROP table ".sql_table('plug_blogmenu_template'));
		}		
	}
	
	function getInstallRankConfig()
	{
		return array(
			'blogmax'     => 20 ,
			'blogdefault' => 10 ,
			'catmax'      => 20 ,
			'catdefault'  => 10 
		);
	}


	function getModuleList() {
		$module_str = $this->getOption('modules');
		if (trim($module_str)) {
			return explode(',', $module_str);
		}
		return array();
	}
	
	function getModule($name) {
		if (!isset($this->modules[$name])) {
			$this->_loadModule($name);
		}
		return $this->modules[$name];
	}

	function _loadModule($name) {
		$module_class = 'BMModule_' . $name;
		$module_file = $this->getDirectory() . 'modules/' .$module_class . '.php';
		if (file_exists($module_file)) {
			include_once($module_file);
			if (class_exists($module_class)) {
				$this->modules[$name] = new $module_class;
				return;
			}
		}
		$this->modules[$name] = false;
	}

	function &getTemplate($name)
	{
		if (!isset($this->templates[$name])) {
			$res = sql_query('SELECT * FROM '.sql_table('plug_blogmenu_template')
			     . ' WHERE tname="' . sql_real_escape_string($name) . '"');
			if ($res && sql_num_rows($res) > 0) {
				$this->templates[$name] = sql_fetch_assoc($res);
				$this->templates[$name] = array_map(array(&$this, '_parseSkinfile'), $this->templates[$name]);
			} else {
				$this->templates[$name] = false;
			}
		}
		return $this->templates[$name];
	}
	
	function clearTemplate($name)
	{
		unset($this->templates[$name]);
	}
	
	function _parseSkinfile($file)
	{
		global $CONF;
		
		$rep = $CONF['SkinsURL'] . PARSER::getProperty('IncludePrefix') . "$2";
		return preg_replace("/(<%skinfile\(?)([^\(\)]+)?(\)?%>)/", $rep, $file);
		
	}

	function loadOptions()
	{
		if (!is_array($this->opt)) {
			$this->opt = array();
			$this->opt['paramorder'] = $this->getOption('paramorder');
			$this->opt['addindex'] = ($this->getOption('add_indexphp') == 'yes');
			$this->opt['nodefbid'] = ($this->getOption('add_defblogid') == 'no');
			$this->opt['nodefbid_cat'] = ($this->getOption('add_defblogid_cat') == 'no');
			$this->opt['nobid'] = ($this->getOption('add_blogid') == 'no');
			$this->opt['nobid_cat'] = ($this->getOption('add_blogid_cat') == 'no');
		}
		return $this->opt;
	}
	
	function getDefaultBlogURL()
	{
		global $CONF;
		
		if (!$this->defblogurl) {
			$this->defblogurl = quickQuery('SELECT burl as result FROM '.sql_table('blog').' WHERE bnumber='.$CONF['DefaultBlog']);
			if (!$this->defblogurl) {
				$this->defblogurl = $CONF['Self'];
			}
		}
		return $this->defblogurl;
	}
	
	
	function init ()
	{
		$this->opt = false;
		$this->templates = array();
		$this->modules = array();
		$this->defblogurl = false;
	}
	
	
	function event_QuickMenu(&$data)
	{
		// only show when option enabled
		if ($this->getOption('quickmenu') != 'yes') return;
		
		global $member;
		// only show to admins
		if (!($member->isLoggedIn() && $member->isAdmin())) return;
		
		array_push(
			$data['options'],
			array(
				'title' => 'Blog Menu',
				'url' => $this->getAdminURL(),
				'tooltip' => 'Edit Blog Menu'
			)
		);
	}

	function event_BlogSettingsFormExtras($data)
	{
		global $CONF, $member;
		
		if ($this->getOption('show_extst_rank') != 'yes') return;
		
		$blogid = $data['blog']->getID();
		if (!$member->isBlogAdmin($blogid)) return;
		
		$langfile = $this->getDirectory() . 'language/'.$CONF['Language'].'.php';
		if (!file_exists($langfile)) {
			$langfile = $this->getDirectory() . 'language/english.php';
		}
		include_once($langfile);
		
		$maxrank = $this->getOption('maxcatrank');
		$order = $this->getOption('catorder');
		$rank = array();
		$query = 'SELECT c.cname as name, r.rcid as id, r.rank as rank'
			. ' FROM '.sql_table('plug_blogmenu_rank').' as r, '.sql_table('category').' as c'
			. ' WHERE c.cblog='.intval($blogid).' and r.rcid=c.catid and r.rcontext="category"'
			. ' ORDER BY r.rank ASC, '.$order;
		$res = sql_query($query);
		while ($a = sql_fetch_assoc($res)) {
			$rank[] = $a;
		}
		
		?>
		<h4 id="np_blogmenu">Blog Menu</h4>
		<form method="post" action="<?php echo $this->getAdminURL(); ?>index.php">
		<div>
			<input name="action" value="rankupdate" type="hidden" />
			<input name="context" value="category" type="hidden" />
			<input name="blogid" value="<?php echo $blogid ?>" type="hidden" />
			<input name="from" value="blogsettings" type="hidden" />
			
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
		</div>
		</form>
		
		<?php
	}

	function event_PostAddBlog($data)
	{
		$b =& $data['blog'];
		$blogid = $b->getID();
		$defrank = $this->getOption('defblogrank');
		if (!$defrank) $defrank = $this->getInstallDefBlogRank();
		
		$res = sql_query('INSERT INTO '.sql_table('plug_blogmenu_rank').' SET '
						   . 'rcid='.intval($blogid).', rank='.intval($defrank).', rcontext="blog"');
		if(!$res) {
			ACTIONLOG::add(WARNING, 'NP_BlogMenu : '.sql_error());
		}
	}
	
	function event_PostAddCategory($data) {
		$defrank = $this->getOption('defcatrank');
		if (!$defrank) $defrank = $this->getInstallDefCatRank();
		
		$res = sql_query('INSERT INTO '.sql_table('plug_blogmenu_rank').' SET '
				. 'rcid='.intval($data['catid']).', rank='.intval($defrank).', rcontext="category"');
		if(!$res) {
			ACTIONLOG::add(WARNING, 'NP_BlogMenu : '.sql_error());
		}
	}

	function event_PostDeleteBlog($data) {
		$res = sql_query('DELETE FROM '.sql_table('plug_blogmenu_rank')
						   .' WHERE rcontext="blog"'
						   .' and rcid='.intval($data['blogid']));
		if(!$res) {
			ACTIONLOG::add(WARNING, 'NP_BlogMenu : '.sql_error());
		}
	}
	
	function event_PostDeleteCategory($data) {
		$res = sql_query('DELETE FROM '.sql_table('plug_blogmenu_rank')
						   .' WHERE rcontext="category"'
						   .' and rcid='.intval($data['catid']));
		if(!$res) {
			ACTIONLOG::add(WARNING, 'NP_BlogMenu : '.sql_error());
		}
	}

	
	function doSkinVar($skinType, $template, $expand_opt='', $blogvisible='', 
		$narrow=0, $blogorder='', $catorder='')
	{
		global $CONF, $manager, $blog;
		
		if ($template) $tp = $this->getTemplate($template);
		if (!isset($tp) || !$tp)
			echo '<p>Please specify the template name.</p>';

		$opt = $this->loadOptions();
		
		if ($blog) {
			$b =& $blog;
		} else {
			$b =& $manager->getBlog($CONF['DefaultBlog']);
		}

		$pageblogid = intval($b->getID());
		$narrow = intval($narrow);
		
		$defblogurl = $this->getDefaultBlogURL();
		
		$open_all = false;
		$open_blogs = array();
		$close_blogs = array();

		if ($expand_opt == '@')
		{
			$open_blogs[] = $pageblogid;
		}
		elseif (preg_match("{^(<>)?([0-9/]+)$}", $expand_opt, $m))
		{
			if ($m[1]) {
				$close_blogs = explode('/', $m[2]);
			} else {
				$open_blogs = explode('/', $expand_opt);
			}
		}
		else
		{
			$open_all = true;
		}

		if (preg_match("{^(<>)?([0-9/]+)$}", $blogvisible, $m))
		{
			if ($m[1]) {
				$bwhere = 'b.bnumber NOT IN('.str_replace('/',',', $m[2]).')';
			} else {
				$bwhere = 'b.bnumber IN('.str_replace('/',',', $m[0]).')';
				$static_order = 'ORDER BY FIELD(b.bnumber,'.str_replace('/',',', $m[0]).')';
			}
		}

		if (preg_match('/(bnumber|bname) +(asc|desc)/i', $blogorder, $m)) {
			$blogorder = $m[1].' '.$m[2];
		} else {
			$blogorder = $this->getOption('blogorder');
		}
		
		if (preg_match('/(catid|cname) +(asc|desc)/i', $catorder, $m)) {
			$catorder = $m[1].' '.$m[2];
		} else {
			$catorder = $this->getOption('catorder');
		}

		$usePathInfo = ($CONF['URLMode'] == 'pathinfo');
		
		// Bloglist start -----------------------------------------------------
		
		echo $tp['blogheader'];
		
		$query = 'SELECT b.bnumber as blogid, b.bname as blogname, b.burl as blogurl, b.bshortname, b.bdesc as blogdesc'
		       . ' FROM '.sql_table('blog').' as b,'
		       . sql_table('plug_blogmenu_rank').' as r'
		       . ' WHERE r.rcid=b.bnumber and r.rcontext="blog"';
		
		if (isset($bwhere)) {
			$query .= ' and '. $bwhere;
		} else {
			$query .= ' and r.rank<1000';
		}
		if (isset($static_order)) {
			$query .= ' ' . $static_order;
		} else {
			$query .= ' ORDER BY r.rank, b.' . sql_real_escape_string($blogorder);
		}
		
		$aliases = $this->_getBnameAliases($tp);
		
		$res = sql_query($query);
		while ($data = sql_fetch_assoc($res)) {

			if (!$data['blogurl']) {
				$data['blogurl'] = $defblogurl;
			}
			$data['bloglink'] = $data['blogurl'];
			
			$isDefaultBlog = ($data['blogid'] == $CONF['DefaultBlog']);
			$isDefaultUrl = ($data['blogurl'] == $defblogurl);
			if (!$isDefaultUrl
				&& preg_match('{^'.$defblogurl.'index.php$}', $data['blogurl']))
			{
				$isDefaultUrl = true;
			}
			
			$replace_blogurl = $data['blogurl'];
			
			if (!$usePathInfo)
			{
				if ($opt['addindex'] && substr($data['blogurl'], -1) == '/')
				{
					$data['bloglink'] = $data['blogurl'] . 'index.php';
					$replace_blogurl = $data['bloglink'];
				}
			}
			
			
			// replace $CONF['BlogURL'] to current parsing blog url
			$OLD_BLOGURL = $CONF['BlogURL'];
			$this->_replaceConfURL($replace_blogurl);
			
			
			$data['archivedate'] = '';
			
			if ((!$isDefaultBlog && (!$opt['nobid'] || $isDefaultUrl))
				|| ($isDefaultBlog && !$opt['nodefbid']))
			{
				$data['bloglink'] = createBlogidLink($data['blogid']);
			}
			elseif (intval($narrow) && ($skinType == 'archive' || $skinType == 'archivelist'))
			{
				switch ($skinType) {
					case 'archivelist':
						$data['bloglink'] = createArchiveListLink($data['blogid']);
						break;
					case 'archive':
						global $archive;
						$data['bloglink'] = createArchiveLink($data['blogid'], $archive);
						$data['archivedate'] = $archive;
						break;
				}
			}
			elseif ($data['bloglink'] != $data['blogurl'])
			{
				$data['bloglink'] = $data['blogurl'];
			}
			
			$data['self'] = $CONF['Self'];
			
			if (array_key_exists($data['blogid'], $aliases)) {
				$data['blogname'] = $aliases[$data['blogid']];
			}
			
			if ($pageblogid == $data['blogid']) {
				$data['flag'] = $tp['blogflag'];
				$data['flag'] = str_replace('<%flag%>', '', $data['flag']);
				$data['flag'] = TEMPLATE::fill($data['flag'], $data);
			}
			
			
			// Categorylist start ---------------------------------------------
			$tp_pieces = preg_split('/<%%|%%>/', $tp['bloglist']);
			$maxidx = sizeof($tp_pieces);
			
			for ($idx = 0; $idx<$maxidx; $idx++) {
				
				echo TEMPLATE::fill($tp_pieces[$idx], $data);
				
				$idx++;
				if ($idx >= $maxidx) break;
				
				if (preg_match('/^([^(]+)\((.*)\)$/', $tp_pieces[$idx], $m)) {
					$plugname = $m[1];
					$plugparams = explode(',',$m[2]);
					$plugparams = array_map('trim', $plugparams);
				} else {
					$plugname = $tp_pieces[$idx];
					$plugparams = array();
				}
				
				// close
				if (!$open_all && 
				      (in_array($data['blogid'], $close_blogs)
				          || (count($close_blogs) < 1
				              && !in_array($data['blogid'], $open_blogs))))
				{
					if (substr($plugname, 0, 1) == '+') {
						$plugname = substr($plugname, 1);
					} else {
						continue;
					}
				}
				elseif (substr($plugname, 0, 1) == '+')
				{
					$plugname = substr($plugname, 1);
				}
				
				// call a plugin
				if (!in_array($plugname, $this->getModuleList()))
				{
					$plugparams = TEMPLATE::fill($plugparams, $data);
					$this->_call_plugin($skinType, $plugname, $plugparams, $pageblogid, $data['blogid']);
				}
				
				// module of BlogMenu
				else
				{
					$module =& $this->getModule($plugname);
					if ($module === false) {
						$this->_printDisallow($plugname);
						continue;
					}
					
					$info = array(
						'skinType' => $skinType,
						'template' => $template,
						'pageblogid' => $pageblogid,
						'order' => $catorder,
						'narrow' => $narrow
					);
					$module->doParse($this, $data, $info, $plugparams);
				}
				
			}
			// Categorylist end -----------------------------------------------
			
			
			// restore $CONF['BlogURL']
			$this->_replaceConfURL($OLD_BLOGURL);
		}
		
		sql_free_result($res);
		
		echo $tp['blogfooter'];
		
		// Bloglist end -------------------------------------------------------

		if (isset($this->tmp_blog)) unset($this->tmp_blog);
		$this->clearTemplate($template);
	}
	
	
	function _replaceConfURL($url)
	{
		global $CONF;
		$CONF['BlogURL'] = $url;
		$CONF['ItemURL'] = $url;
		$CONF['CategoryURL'] = $url;
		$CONF['ArchiveURL'] = $url;
		$CONF['ArchiveListURL'] = $url;
		$CONF['SearchURL'] = $url;
	}
	

	function _getBnameAliases(&$tp)
	{
		if (trim($tp['aliases'])) {
			$aliases = array();
			$alias_str = explode("\n", trim($tp['aliases']));
			
			foreach($alias_str as $v) {
				list($key, $value) = explode('=', $v);
				if ($value)
					$aliases[intval($key)] = $value;
			}
			
			if (count($aliases)) return $aliases;
		}
		return array();
	}
	
	
	function _call_plugin($skinType, $plugname, $plugparams, $pageblogid, $parseblogid)
	{
		global $manager, $catid, $blog;
		
		if ($manager->pluginInstalled('NP_' . $plugname)) {
			$plugin =& $manager->getPlugin('NP_' . $plugname);
		}
		
		if (isset($plugin) && $plugin)
		{
			array_unshift($plugparams, $skinType);
			
			if ($pageblogid != $parseblogid)
			{
				// replace $blog with copy obj of parsing blog
				if ($blog) {
					// backup
					if (phpversion() < 5) {
						$tmp_blog = $blog;
					} else {
						$tmp_blog = clone($blog);
					}
				}
				$blog = $manager->getBlog($parseblogid);  //copy
				
			}
			
			call_user_func_array(array(&$plugin, 'doSkinVar'), $plugparams);

			if ($pageblogid != $parseblogid)
			{
				// restore $blog (copy)
				if (isset($tmp_blog)) {
					if (phpversion() < 5) {
						$blog = $tmp_blog;
					} else {
						$blog = clone($tmp_blog);
					}
				} else {
					unset($blog);
				}
			}
		}
		else
		{
			$this->_printDisallow($plugname);
		}
		
	}
	
	function _printDisallow($name) {
		echo '<strong>BM DISALLOW ('.htmlspecialchars($name).')</strong>';
	}

}

?>
