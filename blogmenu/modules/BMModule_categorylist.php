<?php

class BMModule_categorylist
{
	function doParse(&$plugin, &$data, $info, $params)
	{
		global $manager, $CONF, $archive;
		
		extract($info);
		
		$b =& $manager->getBlog(intval($pageblogid));
		$usePathInfo = ($CONF['URLMode'] == 'pathinfo');
		
		$opt = $plugin->loadOptions();
		$tp =& $plugin->getTemplate($template);
		$defblogurl = $plugin->getDefaultBlogURL();
		
		$isDefaultBlog = ($data['blogid'] == $CONF['DefaultBlog']);
		$isDefaultUrl = ($data['blogurl'] == $defblogurl);
		
		if ($narrow && $skinType == 'archive' && $archive) {
			sscanf($archive,'%d-%d-%d',$y,$m,$d);
			if ($d) {
				$timestamp_start = mktime(0,0,0,$m,$d,$y);
				$timestamp_end = mktime(0,0,0,$m,$d+1,$y);  
			} elseif ($m) {
				$timestamp_start = mktime(0,0,0,$m,1,$y);
				$timestamp_end = mktime(0,0,0,$m+1,1,$y);
			} else {
				$timestamp_start = mktime(0,0,0,1,1,$y);
				$timestamp_end = mktime(0,0,0,1,1,$y+1);
			}
			
			$query = 'SELECT c.catid as catid, c.cname as catname,'
				. ' c.cdesc as catdesc, count(i.inumber) as amount'
				. ' FROM '.sql_table('category').' as c, '
				. sql_table('plug_blogmenu_rank').' as r, '
				. sql_table('item').' as i '
				. ' WHERE c.cblog='.intval($data['blogid'])
				. ' and c.catid=i.icat'
				. ' and r.rcid=c.catid and r.rcontext="category"'
				. ' and r.rank<1000'
				. ' and i.itime>=' . mysqldate($timestamp_start)
				. ' and i.itime<' . mysqldate($timestamp_end)
				. ' and i.idraft=0'
				. ' GROUP BY c.catid'
				. ' ORDER BY r.rank, c.'. sql_real_escape_string($order);
		}
		else
		{
			$query = 'SELECT c.catid as catid, c.cname as catname,'
				. ' c.cdesc as catdesc, count(i.inumber) as amount'
				. ' FROM '.sql_table('category').' as c, '
				. sql_table('plug_blogmenu_rank').' as r, '
				. sql_table('item').' as i '
				. ' WHERE c.cblog='.intval($data['blogid'])
				. ' and c.catid=i.icat'
				. ' and r.rcid=c.catid and r.rcontext="category"'
				. ' and r.rank<1000'
				. ' and i.itime<=' . mysqldate($b->getCorrectTime())
				. ' and i.idraft=0'
				. ' GROUP BY c.catid'
				. ' ORDER BY r.rank, c.'. sql_real_escape_string($order);
		}
		
		$this->fill($plugin, $data, $skinType, $tp['catheader'], $pageblogid);
		
		$cres = sql_query($query);
		
		while ($cdata = sql_fetch_assoc($cres)) 
		{
			if ($narrow && ($skinType == 'archive' || $skinType == 'archivelist'))
			{
				if ($usePathInfo) {
					$cparams = array($CONF['CategoryKey'] => $cdata['catid']);
				} else {
					$cparams = array('catid' => $cdata['catid']);
				}
				switch ($skinType) {
					case 'archivelist':
						$cdata['catlink'] = createArchiveListLink($data['blogid'], $cparams);
						break;
					case 'archive':
						global $archive;
						$cdata['catlink'] = createArchiveLink($data['blogid'], $archive, $cparams);
						break;
				}
			}
			elseif ((!$isDefaultBlog && (!$opt['nobid_cat'] || $isDefaultUrl))
				|| ($isDefaultBlog && !$opt['nodefbid_cat']))
			{
				if ($opt['paramorder'] == 'blog-cat')
				{
					if ($usePathInfo) {
						$cparams = array($CONF['CategoryKey'] => $cdata['catid']);
					} else {
						$cparams = array('catid' => $cdata['catid']);
					}
					$cdata['catlink'] = createBlogidLink($data['blogid'], $cparams);
				}
				else
				{
					if ($usePathInfo) {
						$cparams = array(
							$CONF['BlogKey'] => $data['blogid']
							);
					} else {
						$cparams = array('blogid' => $data['blogid']);
					}
					$cdata['catlink'] = createCategoryLink($cdata['catid'], $cparams);
				}
			}
			else
			{
				$cdata['catlink'] = createCategoryLink($cdata['catid']);
			}
			
			$merge_data = array_merge($data,$cdata);
			
			if ($catid == $cdata['catid']) {
				$merge_data['catflag']= $tp['catflag'];
				$merge_data['catflag'] = str_replace('<%catflag%>', '', $merge_data['catflag']);
				$merge_data['catflag'] = TEMPLATE::fill($merge_data['catflag'], $merge_data);
			}
			
			$this->fill($plugin, $merge_data, $skinType, $tp['catlist'], $pageblogid);
		
		}

		sql_free_result($cres);
				
		$this->fill($plugin, $data, $skinType, $tp['catfooter'], $pageblogid);
	}
	
	
	function fill(&$plugin, &$data, $skinType, $template, $pageblogid)
	{
		global $CONF;
		
		$tp_pieces = preg_split('/<%%|%%>/', $template);
		$maxidx = sizeof($tp_pieces);
		
		for ($idx = 0;$idx<$maxidx;$idx++) {
			
			echo TEMPLATE::fill($tp_pieces[$idx], $data);
			
			$idx++;
			if ($idx >= $maxidx) break;
			
			if (preg_match('/^([^(]+)\((.*)\)$/', $tp_pieces[$idx], $m)) {
				$plugname = $m[1];
				$plugparams = explode(',',$m[2]);
				$plugparams = array_map('trim', $plugparams);
			} else {
				$plugname = $plugaction;
				$plugparams = array();
			}
			if (substr($plugname, 0, 1) == '+') {
				$plugname = substr($plugname, 1);
			}
			
			$plugparams = TEMPLATE::fill($plugparams, $data);
			$plugin->_call_plugin($skinType, $plugname, $plugparams, $pageblogid, $data['blogid']);
			
		}
		
	}
	
}

?>