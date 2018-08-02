<?php
class SQLClass
{
	protected $_columns = array();
	protected $_table;
	protected $_new = false;
	protected $_userLink = false;

	public $_errors = array();

	public function __construct($_values=FALSE, $_pass_to_intiliazing=FALSE)
	{
		$this->initializeValues($_pass_to_intiliazing);
		if(isset($_values) && $_values)
		{
			if(isset($_values["id"]) && is_numeric($_values["id"]) && $_values["id"] != 0)
			{
				$this->SELECT($_values["id"]);
				$this->setValues($_values);
			}
			elseif(is_numeric($_values) && $_values != 0) $this->SELECT($_values);
			else
			{
				$this->setValues($_values);
				$this->setNewId();
			}
		}
		else $this->setNewId();
		$this->SELECT_LINK();
		$this->AfterCONSTRUCT();
	}

	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		return TRUE;
	}

	protected function AfterCONSTRUCT()
	{
		return true;
	}

	protected function addColumn($_name, $_type=FALSE, $_value=FALSE, $_size=FALSE, $_empty=false)
	{
		if($_name)
		{
			if($_name == "id" || $_type == "id") 			$this->_columns[$_name] = new Column(0,10,false);
			elseif($_type == "id_empty") 					$this->_columns[$_name] = new Column(0,10,true);
			elseif($_type == "boolean") 					$this->_columns[$_name] = new Column(0,1,true);
			elseif($_name == "title" || $_type == "title") 	$this->_columns[$_name] = new Column("",100,false);
			elseif($_type == "url") 						$this->_columns[$_name] = new Column("",500,true);
			elseif($_type == "datetime") 					$this->_columns[$_name] = new Column("0000-00-00 00:00:00",500,true);
			elseif($_type == "TXT") 					$this->_columns[$_name] = new Column("",16383,true);

			elseif($_value !== FALSE && $_size !== FALSE)	$this->_columns[$_name] = new Column($_value,$_size,$_empty);
			else return FALSE;
		}else return FALSE;
		return TRUE;
	}

	public function getColumnNames()
	{
		$columns = array();
		foreach ($this->_columns as $column => $value) {
			$columns[$column] = $column;
		}
		return $columns;
	}

	public function getTable(){ return $this->_table; }


	public function setValue($_column, $_value)
	{
		if(isset($this->_columns[$_column]))
		{
			$this->_columns[$_column]->setValue(toNO($_value));
		}
	}

	public function getValue($_column)
	{
		return (isset($this->_columns[$_column]))?$this->_columns[$_column]->getValue():FALSE;
	}

	public function setLinkedValue($_column, $_value)
	{
		if($this->_userLink && $this->_userLink->_columns[$_column]) $this->_userLink->_columns[$_column]->setValue($_value);
	}

	public function getLinkedValue($_column)
	{
		return ($this->_userLink && $this->_userLink->_columns[$_column])?$this->_userLink->_columns[$_column]->getValue():FALSE;
	}

	public function getUserLink()
	{
		return $this->_userLink;
	}

	public function getID() { return $this->getValue("id"); }

	public function getLinkedID() { return $this->getLinkedValue("id"); }

	public function setValues($_values)
	{
		foreach($_values as $column => $value) $this->setValue($column, $value);
	}

	public function SELECT_LINK()
	{
		$userID = getUserID();
		$table = $this->getTable();
		if($userID && ($table==="mal" || $table==="gift"))
		{
			$id = $this->getID();
			$linkID = SQL_GET_ID("user_link", $table."_id='".$id."' AND user_id='".$userID."'");
			$this->_userLink = new UserLink($linkID);
			$this->setLinkedValue("user_id", $userID);
		}else return FALSE;
	}

	public function getValues()
	{
		$values = array();
		foreach($this->_columns as $name => $column) $values[$name] = $column->getValue();
		return $values;
	}

	public function getMaxSize($_column)
	{
		return ($this->_columns[$_column]->getSize())?$this->_columns[$_column]->getSize():false;
	}

	protected function BeforeCOMMIT()
	{
		return true;
	}

	private function CHECK()
	{
		$return = TRUE;
		foreach($this->_columns as $key => $_column)
		{
			if(!$_column->check())
			{
				$this->setError("CHECK", $key, $text = "Check ERROR!");
				$return = FALSE;
			}
		}
		return $return;
	}

	public function setError($function, $column, $text = "")
	{
		$this->_errors[$column][$function] = $text;
	}

	public function DEBUG()
	{
		debug($this->_errors);
		debug($this->getValues());
	}

	public function getErrors()
	{
		return $this->_errors;
	}

	public function clearErrors()
	{
		$this->_errors = array();
	}
	public function getErrorColumns()
	{
		$data = array();
		$errors = $this->getErrors();
		if(is_array($errors))
		{
			foreach ($errors as $col => $functions) {
				$data[$col] = $col;
			}
			$data = array_values($data);
		}

		return $data;
	}

	public function COMMIT()
	{
		if($this->BeforeCOMMIT() && $this->CHECK())
		{
			$id = ($this->isOld())?$this->getID():FALSE;
			$id = SQL_UPDATE_OR_ADD_NEW($this->getTable(), $this->getValues(), $id);
			if($id)
			{
				$this->SELECT($id);
				return $this->AfterCOMMIT();
			}else return FALSE;
		}else return FALSE;
	}

	protected function AfterCOMMIT()
	{
		return true;
	}

	public function REMOVE()
	{
		$this->setValue("active", 0);

		if($this->isOld() && SQL_UPDATE_OR_ADD_NEW($this->getTable(), $this->getValues(), $this->getID()))
		{
			return $this->AfterREMOVE();
		}else return FALSE;
	}

	protected function AfterREMOVE()
	{
		return true;
	}

	public function SELECT($_id = FALSE)
	{
		$id = ($_id)?$_id:$this->getID();
		$values = SQL_SELECT_ID($this->getTable(), $id);
		if(is_array($values)) $this->setValues($values);
	}

	public function isOld()
	{
		return SQL_CONTAINS_WHERE($_table=$this->getTable(), $_where="id='" . $this->getValue("id") . "'");
	}

	public function setNewId()
	{
		$id = SQL_GET_NEXT_ID($this->getTable());
		if(is_numeric($id))
		{
			if($id === 0) $id = 1;
			$this->setValue("id", $id);
		}
	}

	public function inDataspace()
	{
		$where = "";
		$valuePairs = array();
		foreach($this->getValues() as $column => $value)
		{
			$valuePairs[] = "`". $column . "`='" . $value . "'";
		}
		$where = implode(" AND ",$valuePairs);
		return SQL_CONTAINS_WHERE($_table=$this->getTable(), $_where=$where);
	}

}
?>
