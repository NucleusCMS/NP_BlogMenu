<?php

class PlugTemplate
{
	var $table;
	var $primarykey;
	var $idkey;
	var $desckey;
	
	function PlugTemplate($table, $primarykey, $namekey, $descriptionkey='')
	{
		$this->table = $table;
		$this->idkey = '`'.$primarykey.'`';
		$this->namekey = '`'.$namekey.'`';
		$this->desckey = '`'.$descriptionkey.'`';
	}

	function getIdFromName($name)
	{
		return quickQuery('SELECT '.$this->idkey.' as result FROM '.$this->table.' WHERE '.$this->namekey.'="'.sql_real_escape_string($name).'"');
	}

	function getNameFromID($id)
	{
		return quickQuery('SELECT '.$this->namekey.' as result FROM '.$this->table.' WHERE '.$this->idkey.'='.intval($id));
	}
	
	function getDataFromID($dataname, $id)
	{
		return quickQuery('SELECT `'.sql_real_escape_string($dataname).'` as result FROM '.$this->table.' WHERE '.$this->idkey.'='.intval($id));
	}
	
	function exists($name)
	{
		$res = sql_query('SELECT * FROM '.$this->table.' WHERE '.$this->namekey.'="'.sql_real_escape_string($name).'"');
		return (sql_num_rows($res) != 0);
	}
	
	function existsID($id)
	{
		$res = sql_query('select * FROM '.$this->table.' WHERE '.$this->idkey.'='.intval($id));
		return (sql_num_rows($res) != 0);
	}

	function getTemplateList($w='', $s='')
	{
		$where = '';
		if ($w != '') $where = ' WHERE '.$w;
		
		$query = 'SELECT '.$this->idkey.' as id, '.$this->namekey.' as name';
		if ($this->desckey) $query .= ', '.$this->desckey.' as description';
		if ($s) $query .= ', '.$s;
		$query .= ' FROM '.$this->table. $where .' ORDER BY '.$this->namekey;

		$res = sql_query($query);
		$templates = array();
		while ($a = sql_fetch_assoc($res)) {
			$templates[] = $a;
		}
		return $templates;
	}

	function getTemplateDesc($id)
	{
		$where = ' WHERE '.$this->idkey.'='.intval($id);
		$query = 'SELECT '.$this->idkey.' as id, '.$this->namekey.' as name';
		if ($this->desckey) $query .= ', '.$this->desckey.' as description';
		$query .= ' FROM '.$this->table. $where .' ORDER BY '.$this->namekey;

		$res = sql_query($query);
		return sql_fetch_assoc($res);
	}

	function read($name)
	{
		$query = 'SELECT * FROM '.$this->table.' WHERE '.$this->namekey.'="'.sql_real_escape_string($name).'"';
		$res = sql_query($query);
		return sql_fetch_assoc($res);
	}

	function readFromID($id)
	{
		$query = 'SELECT * FROM '.$this->table.' WHERE '.$this->idkey.'='.intval($id);
		$res = sql_query($query);
		return sql_fetch_assoc($res);
	}

	function createTemplate($name, $desc='')
	{
		$query = 'INSERT INTO '.$this->table.' SET '.$this->namekey.'="'. sql_real_escape_string($name).'"';
		if ($desc && $this->desckey) $query .= ', '.$this->desckey.'="'.sql_real_escape_string($desc).'"';
		sql_query($query);
		$newid = sql_insert_id();
		return $newid;
	}

	function updateTemplate($id, $template)
	{
		$query = 'UPDATE '.$this->table.' SET ';
		foreach ($template as $k => $v) {
			$query .= $k.'="'.sql_real_escape_string($v).'",';
		}
		$query = substr($query,0,-1);
		$query .= ' WHERE '.$this->idkey.'='.intval($id);
		sql_query($query);
	}

	function deleteTemplate($id)
	{
		sql_query('DELETE FROM '.$this->table.' WHERE '.$this->idkey.'=' . intval($id));
	}

}
