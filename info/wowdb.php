<?php
/******************************
 * WoWRoster.net  Roster
 * Copyright 2002-2006
 * Licensed under the Creative Commons
 * "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * Short summary
 *  http://creativecommons.org/licenses/by-nc-sa/2.5/
 *
 * Full license information
 *  http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
 * -----------------------------
 *
 * $Id: wowdb.php 374 2006-12-23 21:50:48Z zanix $
 *
 ******************************/

class wowdb
{
	var $db;			// Database resource id
	var $assignstr;		// Data to be inserted/updated to the db
	var $sqldebug;		//
	var $sqlstrings;	// Array of SQL strings passed to query()
	var $messages;		// Array of stored messages
	var $errors;		// Array of stored error messages
	var $membersadded=0;
	var $membersupdated=0;
	var $membersremoved=0;

	function build_query( $query , $array = false )
	{
		if( !is_array($array) )
		{
			return false;
		}

		$fields = array();
		$values = array();

		if( $query == 'INSERT' )
		{
			foreach( $array as $field => $value )
			{
				$fields[] = "`$field`";

				if( is_null($value) )
				{
					$values[] = 'NULL';
				}
				elseif( is_string($value) )
				{
					$values[] = "'" . $this->escape($value) . "'";
				}
				else
				{
					$values[] = ( is_bool($value) ) ? intval($value) : $value;
				}
			}

			$query = ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
		}
		elseif( $query == 'UPDATE' )
		{
			foreach( $array as $field => $value )
			{
				if( is_null($value) )
				{
					$values[] = "`$field` = NULL";
				}
				elseif( is_string($value) )
				{
					$values[] = "`$field` = '" . $this->escape($value) . "'";
				}
				else
				{
					$values[] = ( is_bool($value) ) ? "`$field` = " . intval($value) : "`$field` = $value";
				}
			}

			$query = implode(', ', $values);
		}

		return $query;
	}
	/**
	 * Connect to the database, and select it if $name is provided
	 *
	 * @param string $host MySQL server host name
	 * @param string $user MySQL server user name
	 * @param string $password MySQL server user password
	 * @param string $name MySQL server database name to select
	 * @return bool
	 */
	function connect( $host, $user, $password, $name=null )
	{
		$this->db = @mysql_connect($host, $user, $password);

		if( $this->db )
		{
			if ( !is_null($name) )
			{
				$db_selected = @mysql_select_db( $name,$this->db );
				if( $db_selected )
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}


	/**
	 * Front-end for SQL_query
	 *
	 * @param string $querystr
	 * @return mixed $result handle
	 */
	function query( $querystr )
	{
		$this->sqlstrings[] = $querystr;

		$result = @mysql_query($querystr,$this->db);
		return $result;
	}


	/**
	 * Get last SQL error
	 *
	 * @return string last SQL error
	 */
	function error()
	{
		$result = @mysql_errno().': '.mysql_error();
		return $result;
	}


	/**
	 * Front-end for SQL_fetch_assoc
	 *
	 * @param int $result handle
	 * @return array current db row
	 */
	function getrow( $result )
	{
		global $roster_conf;

		// die quietly if debugging is on and we've got an invalid result. The page may
		// render correctly with just an error printed, so if debugging is off we don't die.
		if (!$result && $roster_conf['debug_mode'])
		{
			die_quietly('Invalid query result passed','Roster DB Layer');
		}

		return mysql_fetch_assoc( $result );
	}


	/**
	 * Front-end for SQL_fetch_assoc
	 *
	 * @param int $result handle
	 * @return array current db row
	 */
	function fetch_assoc( $result )
	{
		global $roster_conf;

		// die quietly if debugging is on and we've got an invalid result. The page may
		// render correctly with just an error printed, so if debugging is off we don't die.
		if (!$result )
		{
			die_quietly('Invalid query result passed','Roster DB Layer');
		}

		return mysql_fetch_assoc( $result );
	}
	function fetch( $result )
	{
		global $roster_conf;

		// die quietly if debugging is on and we've got an invalid result. The page may
		// render correctly with just an error printed, so if debugging is off we don't die.
		if (!$result )
		{
			die_quietly('Invalid query result passed','Roster DB Layer');
		}

		return mysql_fetch_assoc( $result );
	}

function die_quietly( $text='', $title='', $file='', $line='', $sql='' )
{
	global $wowdb, $roster_conf, $wordings;

	// die_quitely died quietly
	if (ROSTER_DIED == 1)
	{
		print '<pre>The quiet die function suffered a fatal error. Die information below'."\n";
		print 'First die data:'."\n";
		print_r($GLOBALS['die_data']);
		print "\n".'Second die data'."\n";
		print_r(func_get_args());
		exit();
	}

	define(ROSTER_DIED,1);

	$GLOBALS['die_data'] = func_get_args();

	if( is_object($wowdb) )
	{
		$wowdb->closeDb();
	}

	if( !empty($title) )
	{
		$header_title = $title;
	}

	if( !defined('HEADER_INC') && is_array($roster_conf) )
	{
		include_once(ROSTER_BASE.'roster_header.tpl');
	}

	if( empty($title) )
	{
		$title = 'Message';
	}

	print border('sred','start',$title).'<table class="bodyline" cellspacing="0" cellpadding="0">'."\n";

	if( !empty($text) )
	{
		print "<tr>\n<td class=\"membersRowRight1\" style=\"white-space:normal;\"><div align=\"center\">$text</div></td>\n</tr>\n";
	}
	if( !empty($sql) )
	{
		print "<tr>\n<td class=\"membersRowRight1\" style=\"white-space:normal;\">SQL:<br />".sql_highlight($sql)."</td>\n</tr>\n";
	}
	if( !empty($file) )
	{
		print "<tr>\n<td class=\"membersRowRight1\">File: $file</td>\n</tr>\n";
	}
	if( !empty($line) )
	{
		print "<tr>\n<td class=\"membersRowRight1\">Line: $line</td>\n</tr>\n";
	}

	if( $roster_conf['debug_mode'] )
	{
		print "<tr>\n<td class=\"membersRowRight1\">";
		backtrace();
		print "</td>\n</tr>\n";
	}

	print "</table>\n".border('sred','end');

	if( is_array($roster_conf) )
	{
		include_once(ROSTER_BASE.'roster_footer.tpl');
	}

	exit();
}
	/**
	 * Front-end for SQL_fetch_array
	 *
	 * @param int $result handle
	 * @return array current db row
	 */
	function fetch_array( $result )
	{
		global $roster_conf;

		// die quietly if debugging is on and we've got an invalid result. The page may
		// render correctly with just an error printed, so if debugging is off we don't die.
		if (!$result && $roster_conf['debug_mode'])
		{
			die_quietly('Invalid query result passed','Roster DB Layer');
		}

		return mysql_fetch_array( $result );
	}


	/**
	 * Front-end for SQL_fetch_row
	 *
	 * @param int $result handle
	 * @return array current db row
	 */
	function fetch_row( $result )
	{
		global $roster_conf;

		// die quietly if debugging is on and we've got an invalid result. The page may
		// render correctly with just an error printed, so if debugging is off we don't die.
		if (!$result && $roster_conf['debug_mode'])
		{
			die_quietly('Invalid query result passed','Roster DB Layer');
		}

		return mysql_fetch_row( $result );
	}


	/**
	 * Front-end for SQL_num_rows
	 *
	 * @param int $result handle
	 * @return array current db row
	 */
	function num_rows( $result )
	{
		global $roster_conf;

		// die quietly if debugging is on and we've got an invalid result. The page may
		// render correctly with just an error printed, so if debugging is off we don't die.
		if (!$result && $roster_conf['debug_mode'])
		{
			die_quietly('Invalid query result passed','Roster DB Layer');
		}

		return mysql_num_rows( $result );
	}


	/**
	 * Front-end to escape string for safe inserting
	 *
	 * @param string $string
	 * @return string escaped
	 */
	function escape( $string )
	{
		if( version_compare( phpversion(), '4.3.0', '>' ) )
			return mysql_real_escape_string( $string );
		else
			return mysql_escape_string( $string );
	}


	/**
	 * Closes the DB connection
	 *
	 * @return unknown
	 */
	function closeDb()
	{
		// Closing connection
		if( is_resource($this->db) )
			return @mysql_close($this->db);
	}


	/**
	 * Frees SQL result memory
	 *
	 * @param int $query_id handle
	 */
	function closeQuery($query_id)
	{
		global $roster_conf;

		// die quietly if debugging is on and we've got an invalid result. The page may
		// render correctly with just an error printed, so if debugging is off we don't die.
		if (!$query_id && $roster_conf['debug_mode'])
		{
			die_quietly('Invalid query result passed','Roster DB Layer');
		}

		// Free resultset
		return @mysql_free_result($query_id);
	}


	/**
	 * Frees SQL result memory
	 *
	 * @param int $query_id handle
	 */
	function free_result($query_id)
	{
		global $roster_conf;

		// die quietly if debugging is on and we've got an invalid result. The page may
		// render correctly with just an error printed, so if debugging is off we don't die.
		if (!$query_id && $roster_conf['debug_mode'])
		{
			die_quietly('Invalid query result passed','Roster DB Layer');
		}

		// Free resultset
		return @mysql_free_result($query_id);
	}


	/**
	 * Returns number of rows affected by an INSERT, UPDATE, or DELETE operation
	 *
	 * @param int $query_id handle
	 */
	function affected_rows()
	{
		return @mysql_affected_rows($this->db);
	}


	/**
	 * Move result pointer
	 *
	 * @param int $result handle
	 * @param int $num row number
	 * @return bool
	 */
	function data_seek($result,$num)
	{
		global $roster_conf;

		// die quietly if debugging is on and we've got an invalid result. The page may
		// render correctly with just an error printed, so if debugging is off we don't die.
		if (!$result && $roster_conf['debug_mode'])
		{
			die_quietly('Invalid query result passed','Roster DB Layer');
		}

		return @mysql_data_seek($result, $num);
	}


	/**
	 * Get the ID generated from the previous INSERT operation
	 *
	 * @return int
	 */
	function insert_id()
	{
		return @mysql_insert_id($this->db);
	}


	/**
	 * Sets the SQL Debug output flag
	 *
	 * @param bool $sqldebug
	 */
	function setSQLDebug($sqldebug)
	{
		$this->sqldebug = $sqldebug;
	}


	/**
	 * Returns all messages
	 *
	 * @return string
	 */
	function getSQLStrings()
	{
		$sqlstrings = $this->sqlstrings;
		$output = '';
		if( is_array($sqlstrings) )
		{
			foreach($sqlstrings as $sql)
			{
				$output .= "$sql\n";
			}
		}
		return $output;
	}


/************************
 * Updating Code
************************/


	/**
	 * Resets the SQL insert/update string holder
	 *
	 */
	function reset_values()
	{
		$this->assignstr = '';
	}


	/**
	 * Add a value to an INSERT or UPDATE SQL string
	 *
	 * @param string $row_name
	 * @param string $row_data
	 */
	function add_value( $row_name, $row_data )
	{
		if( $this->assignstr != '' )
			$this->assignstr .= ',';

		$row_data = "'" . $this->escape( $row_data ) . "'";

		$this->assignstr .= " `$row_name` = $row_data";
	}


	/**
	 * Add a time value to an INSERT or UPDATE SQL string
	 *
	 * @param string $row_name
	 * @param array $date
	 */
	function add_time( $row_name, $date )
	{
		if( $this->assignstr != '' )
			$this->assignstr .= ',';

		// 01/01/2000 23:00:00.000
		$row_data = "'".$date['year'].'-'.$date['mon'].'-'.$date['mday'].' '.$date['hours'].':'.$date['minutes'].':00'."'";
		$this->assignstr .= " `$row_name` = $row_data";
	}


	/**
	 * Add a time value to an INSERT or UPDATE SQL string for PVP table
	 *
	 * @param string $row_name
	 * @param string $date
	 */
	function add_pvp2time( $row_name, $date )
	{
		if( $this->assignstr != '' )
			$this->assignstr .= ',';

		$date_str = strtotime($date);
		$p2newdate = date('Y-m-d G:i:s',$date_str);
		$row_data = "'".$p2newdate."'";
		$this->assignstr .= " `$row_name` = $row_data";
	}

} //-END CLASS

$wowdb = new wowdb;
