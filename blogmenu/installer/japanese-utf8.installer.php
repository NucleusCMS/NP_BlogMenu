<?php

class BlogMenuInstaller
{

	function install(&$plugin)
	{
		$rcnf = $plugin->getInstallRankConfig();
		
		// order option
		$plugin->createOption('blogorder', '同じ表示優先度を持つブログの表示順（デフォルト値）', 'select', 'bname asc', 'ブログ名昇順|bname asc|ブログ名降順|bname desc|ブログID昇順|bnumber asc|ブログID降順|bnumber desc');
		$plugin->createOption('catorder', '同じ表示優先度を持つカテゴリーの表示順（デフォルト値）', 'select', 'cname asc', 'カテゴリー名昇順|cname asc|カテゴリー名降順|cname desc|カテゴリーID昇順|catid asc|カテゴリーID降順|catid desc');
		$plugin->createOption('paramorder', 'カテゴリーのURLパラメーターの順序', 'select', 'cat-blog','catid -> blogid|cat-blog|blogid -> catid|blog-cat');
		
		// generate url option
		$plugin->createOption('add_defblogid', 'A-1: デフォルトブログのURLにblogidを付加する', 'yesno', 'no');
		$plugin->createOption('add_defblogid_cat', 'A-2: デフォルトブログに属するカテゴリーのURLにもblogidを付加する', 'yesno', 'no');
		$plugin->createOption('add_blogid', 'B-1: デフォルトブログとは異なるURLを持ったブログのURLにblogidを付加する', 'yesno', 'yes');
		$plugin->createOption('add_blogid_cat', 'B-2: B-1に該当するブログに属するカテゴリーのURLにもblogidを付加する', 'yesno', 'yes');
		$plugin->createOption('add_indexphp', '[ノーマルURL用設定] ブログのURLが "/" で終わる場合、クエリストリング（catid等）の前に "index.php" を追加する', 'yesno', 'yes');
		
		// general
		$plugin->createOption('show_extst_rank', 'Nucleusのブログ設定ページにも、カテゴリー表示優先度の編集フォームを表示する', 'yesno', 'no');
		$plugin->createOption('quickmenu', 'クイックメニューに管理画面へのリンクを表示する', 'yesno', 'yes');
		$plugin->createOption('del_uninstall', 'アンインストール時に、このプラグインのデータベースのテーブルも削除する', 'yesno', 'no');

		// hidden option
		$plugin->createOption('modules', 'Allowed modules', 'text', 'categorylist', 'access=hidden');
		$plugin->createOption('maxblogrank', 'Number of stages of blog display priority', 'text', $rcnf['blogmax'], 'access=hidden');
		$plugin->createOption('defblogrank', 'Default display priority of blog', 'text', $rcnf['blogdefault'], 'access=hidden');
		$plugin->createOption('maxcatrank', 'Number of stages of category display priority', 'text', $rcnf['catmax'], 'access=hidden');
		$plugin->createOption('defcatrank', 'Default display priority of category', 'text', $rcnf['catdefault'], 'access=hidden');
		
		
		sql_query('CREATE TABLE IF NOT EXISTS ' . sql_table('plug_blogmenu_rank'). ' (
			rcid int(11) not null,
			rcontext varchar(20) not null,
			rank int(4) not null,
			PRIMARY KEY (rcid, rcontext))');
		
		sql_query('CREATE TABLE IF NOT EXISTS ' . sql_table('plug_blogmenu_template'). ' (
			tid int(11) not null auto_increment,
			tname varchar(20) not null,
			tdesc varchar(200) not null,
			blogheader text not null, 
			bloglist text not null, 
			blogfooter text not null, 
			blogflag text not null, 
			aliases text not null, 
			catheader text not null, 
			catlist text not null, 
			catfooter text not null, 
			catflag text not null, 
			UNIQUE (tname),
			PRIMARY KEY (tid))');
		
		
		/* insert rank */
		
		// blog
		$query = 'SELECT b.bnumber FROM '.sql_table('blog').' as b'
			. ' LEFT JOIN '.sql_table('plug_blogmenu_rank').' as r'
			. ' ON b.bnumber=r.rcid and r.rcontext="blog" WHERE r.rcid IS NULL';
		$res = sql_query($query);
		$total = mysql_num_rows($res);
		$tmp = '';
		$i = 1;
		while ($row = mysql_fetch_row($res)) {
			if ($total == 1 || $i >= 100) {
				sql_query('INSERT INTO '.sql_table('plug_blogmenu_rank').' (rcontext,rcid,rank) VALUES '.$tmp.' ("blog",'.intval($row[0]).','.$rcnf['blogdefault'].')');
				$tmp = '';
				$i = 1;
			} else {
				$tmp .= ' ("blog",'.intval($row[0]).','.$rcnf['blogdefault'].'),';
				$i ++;
			}
			$total --;
		}
		// clean up
		$query = 'SELECT r.rcid FROM '.sql_table('plug_blogmenu_rank').' as r'
			. ' LEFT JOIN '.sql_table('blog').' as b'
			. ' ON r.rcid=b.bnumber WHERE r.rcontext="blog" and b.bnumber IS NULL';
		$res = sql_query($query);
		while ($row = mysql_fetch_row($res)) {
			sql_query('DELETE FROM '.sql_table('plug_blogmenu_rank').' WHERE rcid='.intval($row[0]).' AND rcontext="blog"');
		}
		
		// category
		$query = 'SELECT c.catid FROM '.sql_table('category').' as c'
			. ' LEFT JOIN '.sql_table('plug_blogmenu_rank').' as r'
			. ' ON c.catid=r.rcid and r.rcontext="category" WHERE r.rcid IS NULL';
		$res = sql_query($query);
		$total = mysql_num_rows($res);
		$tmp = '';
		$i = 1;
		while ($row = mysql_fetch_row($res)) {
			if ($total <= 1 || $i >= 100) {
				sql_query('INSERT INTO '.sql_table('plug_blogmenu_rank').' (rcontext,rcid,rank) VALUES '.$tmp.' ("category",'.intval($row[0]).','.$rcnf['catdefault'].')');
				$tmp = '';
				$i = 1;
			} else {
				$tmp .= ' ("category",'.intval($row[0]).','.$rcnf['catdefault'].'),';
				$i ++;
			}
			$total --;
		}
		// clean up
		$query = 'SELECT r.rcid FROM '.sql_table('plug_blogmenu_rank').' as r'
			. ' LEFT JOIN '.sql_table('category').' as c'
			. ' ON r.rcid=c.catid WHERE r.rcontext="category" and c.catid IS NULL';
		$res = sql_query($query);
		while ($row = mysql_fetch_row($res)) {
			sql_query('DELETE FROM '.sql_table('plug_blogmenu_rank').' WHERE rcid='.intval($row[0]).' AND rcontext="blog"');
		}
		
		
		/* insert default template */
		$check = quickQuery('SELECT count(*) as result FROM ' . sql_table('plug_blogmenu_template'));
		
		if ($check < 1) {
			$query = 'INSERT INTO ' . sql_table('plug_blogmenu_template'). ' SET '
			       . 'tid=1, tname="default", tdesc="Sample template", '
			       . 'blogheader="<ul>\n", '
			       . 'bloglist="<li<%flag%>><a href=\"<%bloglink%>\"><%blogname%></a><%%categorylist%%></li>", '
			       . 'blogfooter="</ul>\n", '
			       . 'blogflag=" class=\"active\"", '
			       . 'catheader="<ul>\n", '
			       . 'catlist="<li<%catflag%>><a href=\"<%catlink%>\"><%catname%></a> (<%amount%>)</li>\n", '
			       . 'catfooter="</ul>\n", '
			       . 'catflag=" class=\"active\""';
			
			sql_query($query);
		}
		
	}
}

?>