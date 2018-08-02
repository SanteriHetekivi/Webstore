<?php
	$SQLClasses = array("Column","SQL","User","Product","ProductType", "Order", "Shipping");
	$SESSIONClasses = array("Rest","Auth", "APP");
	if(!empty($SQLClasses))foreach($SQLClasses as $class)require("classes/SQL/".$class.".class.php");
	if(!empty($SESSIONClasses))foreach($SESSIONClasses as $class)require("classes/Session/".$class.".class.php");

	$ALLClasses = $SQLClasses+$SESSIONClasses;
	function isClass($name)
	{
		return (in_array($name,$GLOBALS['ALLClasses']));
	}

	function classAndTable($_name, $_classOrTable)
	{
		if($_name && $_classOrTable)
		{
			$_name = strtolower($_name);
			$_classOrTable = strtolower($_classOrTable);
			if($_classOrTable == "class")
			{
				if($_name == "product") $return = "product";
				elseif($_name == "producttype") $return = "product_type";
				elseif($_name == "user") $return = "user";
				elseif($_name == "order") $return = "order";
				elseif($_name == "shipping") $return = "shipping";
				else $return = FALSE;
			}
			elseif($_classOrTable == "table")
			{
				if($_name == "product") $return = "Product";
				elseif($_name == "product_type") $return = "ProductType";
				elseif($_name == "user") $return = "User";
				elseif($_name == "order") $return = "Order";
				elseif($_name == "shipping") $return = "Shipping";
				else $return = FALSE;
			}
		}else return FALSE;

		return $return;
	}

	function getObjectByName($name, $id = FALSE)
	{
		$name = strtolower($name);
		if($name == "product") return new Gift($id);
		elseif($name == "producttype") return new GiftType($id);
		elseif($name == "user") return new User($id);
		elseif($name == "order") return new Order($id);
		elseif($name == "shipping") return new Order($id);
		else return FALSE;
	}

	function getEmptyArray($name)
	{
		$name = strtolower($name);
		if($name == "product")
		{
			return array(
				"id" => 0,
				"title" => "",
				"product_type_id" => 0,
				"price" => 0.00,
				"image" => ""
			);
		}
		elseif($name == "producttype")
		{
			return array(
				"id" => 0,
				"title" => ""
			);
		}
		elseif($name == "shipping")
		{
			return array(
				"id" => 0,
				"title" => "",
				"price" => 0.00
			);
		}
		elseif($name == "user")
		{
			return array(
				"id" => 0,
				"username" => "",
				"password" => "",
				"group" => "",
				"data" => ""
			);
		}
		elseif($name == "order")
		{
			return array(
				"id" => 0,
				"product_ids" => "",
				"shipping" => 0,
				"price" => 0.00,
				"shipped" => toMysqlDateTime(0),
				"created" => toMysqlDateTime(0)
			);
		}
		else return FALSE;
	}

	function checkAccess($name, $userGroup)
	{
		$name = strtolower($name);
		if($name == "product")
		{
			return true;
		}
		elseif($name == "producttype")
		{
			return true;
		}
		elseif($name == "shipping")
		{
			return true;
		}
		elseif($name == "user")
		{
			return ($userGroup == "admin");
		}
		elseif($name == "order")
		{
			return ($userGroup == "admin" || $userGroup == "manager" || $userGroup == "worker");
		}
		else return false;
	}

?>
