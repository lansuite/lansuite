<?php
/***************************************************************************
 *                                 db2.php
 *                            -------------------
 *   begin                : Saturday, Feb 13, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id: db2.php,v 1.2 2002/01/28 17:24:45 psotfx Exp $
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if(!defined("SQL_LAYER"))
{

define("SQL_LAYER","db2");

class sql_db
{

	var $db_connect_id;
	var $query_result;
	var $query_resultset;
	var $query_numrows;
	var $next_id;
	var $row = array();
	var $rowset = array();
	var $row_index;
	var $num_queries = 0;

	//
	// Constructor
	//
	function sql_db($sqlserver, $sqluser, $sqlpassword, $database, $persistency = true)
	{
		$this->persistency = $persistency;
		$this->user = $sqluser;
		$this->password = $sqlpassword;
		$this->dbname = $database;

		$this->server = $sqlserver;

		if($this->persistency)
		{
			$this->db_connect_id = odbc_pconnect($this->server, "", "");
		}
		else
		{
			$this->db_connect_id = odbc_connect($this->server, "", "");
		}

		if($this->db_connect_id)
		{
			@odbc_autocommit($this->db_connect_id, off);

			return $this->db_connect_id;
		}
		else
		{
			return false;
		}
	}
	//
	// Other base methods
	//
	function sql_close()
	{
		if($this->db_connect_id)
		{
			if($this->query_result)
			{
				@odbc_free_result($this->query_result);
			}
			$result = @odbc_close($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}


	//
	// Query method
	//
	function sql_query($query = "", $transaction = FALSE)
	{
		//
		// Remove any pre-existing queries
		//
		unset($this->query_result);
		unset($this->row);
		if($query != "")
		{
			$this->num_queries++;

			if(!eregi("^INSERT ",$query))
			{
				if(eregi("LIMIT", $query))
				{
					preg_match("/^(.*)LIMIT ([0-9]+)[, ]*([0-9]+)*/s", $query, $limits);

					$query = $limits[1];
					if($limits[3])
					{
						$row_offset = $limits[2];
						$num_rows = $limits[3];
					}
					else
					{
						$row_offset = 0;
						$num_rows = $limits[2];
					}

					$query .= " FETCH FIRST ".($row_offset+$num_rows)." ROWS ONLY OPTIMIZE FOR ".($row_offset+$num_rows)." ROWS";

					$this->query_result = odbc_exec($this->db_connect_id, $query);

					$query_limit_offset = $row_offset;
					$this->result_numrows[$this->query_result] = $num_rows;
				}
				else
				{
					$this->query_result = odbc_exec($this->db_connect_id, $query);

					$row_offset = 0;
					$this->result_numrows[$this->query_result] = 5E6;
				}

				$result_id = $this->query_result;
				if($this->query_result && eregi("^SELECT", $query))
				{

					for($i = 1; $i < odbc_num_fields($result_id)+1; $i++)
					{
						$this->result_field_names[$result_id][] = odbc_field_name($result_id, $i);
					}

					$i =  $row_offset + 1;
					$k = 0;
					while(odbc_fetch_row($result_id, $i) && $k < $this->result_numrows[$result_id])
					{

						for($j = 1; $j < count($this->result_field_names[$result_id])+1; $j++)
						{
							$this->result_rowset[$result_id][$k][$this->result_field_names[$result_id][$j-1]] = odbc_result($result_id, $j);
						}
						$i++;
						$k++;
					}

					$this->result_numrows[$result_id] = $k;
					$this->row_index[$result_id] = 0;
				}
				else
				{
					$this->result_numrows[$result_id] = @odbc_num_rows($result_id);
					$this->row_index[$result_id] = 0;
				}
			}
			else
			{
				if(eregi("^(INSERT|UPDATE) ", $query))
				{
					$query = preg_replace("/\\\'/s", "''", $query);
				}

				$this->query_result = odbc_exec($this->db_connect_id, $query);

				if($this->query_result)
				{
					$sql_id = "VALUES(IDENTITY_VAL_LOCAL())";

					$id_result = odbc_exec($this->db_connect_id, $sql_id);
					if($id_result)
					{
						$row_result = odbc_fetch_row($id_result);
						if($row_result)
						{
							$this->next_id[$this->query_result] = odbc_result($id_result, 1);
						}
					}
				}

				odbc_commit($this->db_connect_id);

				$this->query_limit_offset[$this->query_result] = 0;
				$this->result_numrows[$this->query_result] = 0;
			}

			return $this->query_result;
		}
		else
		{
			return false;
		}
	}

	//
	// Other query methods
	//
	function sql_numrows($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			return $this->result_numrows[$query_id];
		}
		else
		{
			return false;
		}
	}
	function sql_affectedrows($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			return $this->result_numrows[$query_id];
		}
		else
		{
			return false;
		}
	}
	function sql_numfields($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = count($this->result_field_names[$query_id]);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldname($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = $this->result_field_names[$query_id][$offset];
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldtype($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @odbc_field_type($query_id, $offset);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fetchrow($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			if($this->row_index[$query_id] < $this->result_numrows[$query_id])
			{
				$result = $this->result_rowset[$query_id][$this->row_index[$query_id]];
				$this->row_index[$query_id]++;
				return $result;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	function sql_fetchrowset($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$this->row_index[$query_id] = $this->result_numrows[$query_id];
			return $this->result_rowset[$query_id];
		}
		else
		{
			return false;
		}
	}
	function sql_fetchfield($field, $row = -1, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			if($row < $this->result_numrows[$query_id])
			{
				if($row == -1)
				{
					$getrow = $this->row_index[$query_id]-1;
				}
				else
				{
					$getrow = $row;
				}

				return $this->result_rowset[$query_id][$getrow][$this->result_field_names[$query_id][$field]];

			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	function sql_rowseek($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$this->row_index[$query_id] = 0;
			return true;
		}
		else
		{
			return false;
		}
	}
	function sql_nextid($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			return $this->next_id[$query_id];
		}
		else
		{
			return false;
		}
	}
	function sql_freeresult($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @odbc_free_result($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_error($query_id = 0)
	{
//		$result['code'] = @odbc_error($this->db_connect_id);
//		$result['message'] = @odbc_errormsg($this->db_connect_id);

		return "";
	}

} // class sql_db

} // if ... define

?>