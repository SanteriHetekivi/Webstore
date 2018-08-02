<?php

/**
 *
 */
class App
{
  private $auth = null;
  private $cart = null;

  function __construct()
  {
    if(!isset($_SESSION["auth"])) $_SESSION["auth"]=new Auth();
	  $this->auth = &$_SESSION["auth"];
    $this->cart = new Order();
  }

  private function CHECK($groups = false)
  {
    return (is_object($this->auth))?$this->auth->CHECK($groups):false;
  }

  function SIGNUP($parameters)
  {
    $return = array();
    $success  = false;

    $columns = false;
    if(isset($parameters["User"]) && is_array($parameters["User"]) && !empty($parameters["User"]))
    {
      $columns = $parameters["User"];
    }

    if($columns)
    {
      $columns["userGroup"] = "customer";
      $user = new User($columns);
      $success = $user->COMMIT();
      if($success) $return["data"] = $user->getValue("email");
      else $return["error"]["columns"] = $user->getErrorColumns();
    }
    else
    {
      $tmp = new User();
      $return["error"]["columns"] = $tmp->getColumnNames();
    }


    $return["result"] = $success;

    return $return;
  }

  function LOGIN($parameters)
  {
    $return = array();
    $success  = false;

    if(isset($parameters["username"]) && isset($parameters["password"]))
    {
      $success = (is_object($this->auth) && $this->auth->LOGIN($parameters["username"], $parameters["password"]));
    }

    if($success) $return["data"] = $this->auth->getUserData();
    else $return["error"]["columns"] = ["login.username", "login.password"];


    $return["result"] = $success;

    return $return;
  }

  function LOGOUT($parameters)
  {
    $return = array();

    $success = (is_object($this->auth) && $this->auth->LOGOUT());

    $return["result"] = $success;

    return $return;
  }

  function CHECKLOGIN($parameters)
  {
    $return = array();

    $success = (is_object($this->auth) && $this->auth->CHECK());

    if($success)
    {
      $return["data"] = $this->auth->getUserData();
    }

    $return["result"] = $success;

    return $return;
  }

  function CHECKAUTH($parameters)
  {
    $return = array();

    $page = (isset($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;

    $success = (is_object($this->auth) && $this->auth->checkPage($page));

    if($success)
    {
      $return["data"] = $this->auth->getUserData();
    }

    $return["result"] = $success;

    return $return;
  }

  function GET($parameters)
  {
    $return = array();
    $success  = false;

    $object = (isset($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;
    $id = (isset($parameters["call"]["id"]) && is_numeric($parameters["call"]["id"])) ? $parameters["call"]["id"] : FALSE;
    $userGroup = (is_object($this->auth)) ? $this->auth->getUserGroup() : FALSE;
    $noKeys = (isset($parameters["noKeys"]) && !$parameters["noKeys"]) ? FALSE  : TRUE;

    if($object)
    {
      if(isClass($object) && checkAccess($object, $userGroup) )
      {
        $table = classAndTable($object, "class");
        $where = ($id) ? "AND id = '".$id."' " : "";
        $data = SQL_SELECT(
          $_columns = FALSE,
          $_table=$table,
          $_joinTable = FALSE,
          $_joinTableId = FALSE,
          $_where = "active = '1' " . $where,
          $_order = FALSE,
          $_offset = FALSE,
          $_limit = FALSE,
          $_object = FALSE,
          $_onlyFirstRow = FALSE,
          $_noIDKeys = $noKeys
        );
        $success = ($data !== false);
        $return["data"] = $data;
      }
    }

    $return["result"] = $success;
    return $return;
  }

  function getUserOrders($parameters)
  {
    $return = array();
    $success  = false;

    $userId = (is_object($this->auth)) ? $this->auth->getUserID() : FALSE;

    if($userId)
    {
      $data = SQL_SELECT(
        $_columns = FALSE,
        $_table="order",
        $_joinTable = FALSE,
        $_joinTableId = FALSE,
        $_where = "active = '1' AND user_id = '".$userId."' ",
        $_order = FALSE,
        $_offset = FALSE,
        $_limit = FALSE,
        $_object = FALSE,
        $_onlyFirstRow = FALSE,
        $_noIDKeys = TRUE
      );
      $success = ($data !== false);
      $return["data"] = $data;

    }

    $return["result"] = $success;
    return $return;
  }

  function getOrderProducts($parameters)
  {
    $return = array();
    $success = false;

    $id = (isset($parameters["call"]["object"]) && is_numeric($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;
    $groups = array("admin", "manager", "worker");
    $userGroup = (is_object($this->auth)) ? $this->auth->getUserGroup() : FALSE;

    if($id)
    {
      $order =  new Order($id);
      $data = false;
      if(!$userGroup || !in_array($userGroup, $groups))
      {
        $userId = (is_object($this->auth)) ? $this->auth->getUserID() : FALSE;
        if($userId > 0 && $userId == $order->getValue("user_id"))
        {
          $data = $order->getProducts("array");
        }
      }
      else $data = $order->getProducts("array");

      if(is_array($data))
      {
        $success = true;
        $return["data"] = $data;
      }

    }

    $return["result"] = $success;
    return $return;
  }

  function getProductsByType($parameters)
  {
    $return = array();
    $success  = false;

    $typeid = (isset($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;
    $where = ($typeid && $typeid > 0) ? " AND product_type_id = '".$typeid."'" : "";

    $data =  SQL_SELECT(
      $_columns = FALSE,
      $_table="product",
      $_joinTable = FALSE,
      $_joinTableId = FALSE,
      $_where = "active = '1' " . $where,
      $_order = FALSE,
      $_offset = FALSE,
      $_limit = FALSE,
      $_object = FALSE,
      $_onlyFirstRow = FALSE,
      $_noIDKeys = TRUE
    );
    if(is_array($data))
    {
      $success = true;
      $return["data"] = $data;
    }

    $return["result"] = $success;
    return $return;
  }

  function getUserNames($parameters)
  {
    $return = array();
    $success  = false;

    $groups = array("admin", "manager", "worker");
    $userGroup = (is_object($this->auth)) ? $this->auth->getUserGroup() : FALSE;
    if(in_array($userGroup, $groups))
    {
      $data =  getUserNames();
      if(is_array($data))
      {
        $success = true;
        $return["data"] = $data;
      }
    }

    $return["result"] = $success;
    return $return;
  }

  function getColumn($parameters)
  {
    $return = array();
    $success  = false;

    $object = (isset($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;
    $column = (isset($parameters["call"]["id"])) ? $parameters["call"]["id"] : FALSE;

    $userGroup = (is_object($this->auth)) ? $this->auth->getUserGroup() : FALSE;

    if($object && $column && checkAccess($object, $userGroup))
    {
      $table = classAndTable($object, "class");
      $data =  SQL_SELECT(
        $_columns = $column,
        $_table=$table,
        $_joinTable = FALSE,
        $_joinTableId = FALSE,
        $_where = "active = '1'",
        $_order = FALSE,
        $_offset = FALSE,
        $_limit = FALSE,
        $_object = FALSE,
        $_onlyFirstRow = FALSE,
        $_noIDKeys = FALSE
      );

      if(is_array($data))
      {
        $success = true;
        $return["data"] = $data;
      }
    }

    $return["result"] = $success;
    return $return;
  }

  function EDIT($parameters)
  {
    $return = array();
    $success  = false;

    $object = (isset($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;
    $id = (isset($parameters["call"]["id"]) && is_numeric($parameters["call"]["id"])) ? $parameters["call"]["id"] : FALSE;
    $groups = array("admin", "manager", "worker");
    $userGroup = (is_object($this->auth)) ? $this->auth->getUserGroup() : FALSE;

    if($object && isClass($object) && in_array($userGroup, $groups) && checkAccess($object, $userGroup))
    {
      $id = ($id)?$id:0;
      eval("\$object = new ".$object."(".$id.");");
      if(is_object($object))
      {
        if(isset($parameters["columns"]) && is_array($parameters["columns"]))
        {
          foreach ($parameters["columns"] as $column => $value) $object->setValue($column, $value);
        }
        $success = $object->COMMIT();
        if(!$success)
        {
          $return["error"]["columns"] = $object->getErrorColumns();
        }
      }
    }

    $return["result"] = $success;
    return $return;
  }

  function setShipped($parameters)
  {
    $return = array();
    $success  = false;
    $data = array();

    $id = (isset($parameters["call"]["object"]) && is_numeric($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;
    $groups = array("admin", "manager", "worker");
    $userGroup = (is_object($this->auth)) ? $this->auth->getUserGroup() : FALSE;

    if($id && in_array($userGroup, $groups) && checkAccess("Order", $userGroup))
    {

      $order = new Order($id);
      $order->setShipped();
      $success = $order->COMMIT();

    }

    $return["data"] = $data;
    $return["result"] = $success;
    return $return;
  }

  function REMOVE($parameters)
  {
    $return = array();
    $success  = false;

    $object = (isset($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;
    $id = (isset($parameters["call"]["id"]) && is_numeric($parameters["call"]["id"])) ? $parameters["call"]["id"] : FALSE;
    $groups = array("admin", "manager");
    $userGroup = (is_object($this->auth)) ? $this->auth->getUserGroup() : FALSE;

    $success = false;

    if($object && isClass($object) && $id > 0 && in_array($userGroup, $groups) && checkAccess($object, $userGroup))
    {
      eval("\$object = new ".$object."(".$id.");");
      if(is_object($object))
      {
        $success = $object->REMOVE();
        if(!$success)
        {
          $return["error"]["columns"] = $object->getErrorColumns();
        }
      }
    }

    $return["result"] = $success;
    return $return;
  }

  function removeProduct($parameters)
  {
    $return = array();
    $success  = false;
    $data = array();

    $orderId = (isset($parameters["call"]["object"]) && is_numeric($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;
    $productId = (isset($parameters["call"]["id"]) && is_numeric($parameters["call"]["id"])) ? $parameters["call"]["id"] : FALSE;
    $groups = array("admin", "manager");
    $userGroup = (is_object($this->auth)) ? $this->auth->getUserGroup() : FALSE;

    if($orderId && $productId && in_array($userGroup, $groups) && checkAccess("Order", $userGroup))
    {
      $order = new Order($orderId);
      if($order->removeProduct($productId))
      {
        $success = $order->COMMIT();
        $data = $order->getValues();
      }
    }

    $return["data"] = $data;
    $return["result"] = $success;
    return $return;
  }

  function shoppingCart($parameters)
  {
    $return = array();
    $success  = false;
    $data = array();

    $action = (isset($parameters["call"]["object"])) ? $parameters["call"]["object"] : FALSE;
    $id = (isset($parameters["call"]["id"])) ? $parameters["call"]["id"] : FALSE;

    $userId = (is_object($this->auth)) ? $this->auth->getUserID() : FALSE;
    if($action == "ADD" && $id && $id > 0) $success = $this->cart->addProduct($id);
    elseif($action == "REMOVE" && $id) $success = $this->cart->removeProduct($id);
    elseif($action == "GET")
    {
      $this->cart->countPrice();
      if($id == "COUNT") $data = count($this->cart->getProducts("array"));
      else {
        $data["order"] = $this->cart->getValues();
        $data["products"] = $this->cart->getProducts("array");
        $data["shipping"] = SQL_SELECT(
          $_columns = "title,price",
          $_table="shipping",
          $_joinTable = FALSE,
          $_joinTableId = FALSE,
          $_where = "active = '1'",
          $_order = FALSE,
          $_offset = FALSE,
          $_limit = FALSE,
          $_object = FALSE,
          $_onlyFirstRow = FALSE,
          $_noIDKeys = FALSE
        );

      }
      $success = ($data !== false);
    }
    elseif($action == "PAY")
    {
      if(isset($parameters["object"]) && is_array($parameters["object"]))
      {
        foreach ($parameters["object"] as $column => $value) $this->cart->setValue($column, $value);
      }

      $this->cart->setValue("user_id", $userId);

      $success = $this->cart->COMMIT();
      if($success)
      {
        $this->cart = new Order();
      }
      else
      {
        $return["error"]["columns"] = $this->cart->getErrorColumns();
        $this->cart->clearErrors();
      }
    }

    $return["data"] = $data;
    $return["result"] = $success;
    return $return;
  }
}
?>
