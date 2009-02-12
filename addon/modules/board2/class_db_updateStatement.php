<?php
/*
 * Created on 11.04.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class UpdateStatement
{
	private $tablename;

	private $val_params;
	private $val_valueslist;
	private $val_values;

	private $cond_params;
	private $cond_valueslist;
	private $cond_values;

	/*
	 * Constructor for the Update Statement
	 */
	function updateStatement($tablename) {
		$this->tablename = $tablename;
		$val_params = array();
		$val_valueslist = array();
		$val_values = array();
		$cond_params = array();
		$cond_valueslist = array();
		$cond_values = array();
	}

	/*
	 * Adds a Parameter to the insert statement
	 * $datatype int, string, plain
	 * $param The name of the column in the table
	 * $value The value of the column
	 */
	function addParameter($datatyp, $param, $value){
		if ($value != '') {
			if ($datatype == '') $datatype = 'plain';
			$this->val_params[] .= $param;
			$this->val_values[] .= '%' . $datatyp . '%';
			$this->val_valuelist[] .= $value;
		}
	}

	/*
	 * Adds a Parameter to the insert statement
	 * $datatype int, string, plain
	 * $param The name of the column in the table
	 * $value The value of the column
	 */
	function addCondition($datatyp, $param, $value){
		if ($value != '') {
			if ($datatype == '') $datatype = 'plain';
			$this->cond_params[] .= $param;
			$this->cond_values[] .= '%' . $datatyp . '%';
			$this->cond_valuelist[] .= $value;
		}
	}
		
	/*
	 * Executes the Inserstatement in the DB and returns the $query_id
	 * $condition e.g.
	 *  .execute('(colum1 = 3)')
	 *  .execute('(colum1 = 3) AND (colum2 = 44)')
	 */
	function execute(){
		global $db;

		if (count($this->val_params) == 0) {
			throw new Exception('To less values - class_db_updateStatement');
		} else {
			$sql = 'UPDATE `' . $this->tablename . '` SET ';

			foreach ($this->val_params as $id => $columnname) {
				$sql .= '`' . $columnname . '`= ';
				$sql .= $this->val_values[$id] . ', ';
			}
			$sql = substr($sql, 0, strlen($sql)-2);

			if (count($this->cond_params) > 0) {
				$condition = '';
				foreach ($this->cond_params as $id => $columnname) {
					$condition .= '`' . $columnname . '`= ';
					$condition .= $this->cond_values[$id] . ' AND ';

					$this->val_valuelist[] .= $this->cond_valuelist[$id];
				}
				$condition = substr($condition, 0, -5);
				$sql = $sql . ' WHERE '. $condition . ';';
			}

			return $db->qry($sql , $this->val_valuelist);
		}
	}
}

//
// Please make a history at the end of file of your changes !!
//

/* HISTORY
 * 06. 2. 2009 : Created the file.
 */
?>
