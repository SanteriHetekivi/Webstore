<?php

/**
 *
 */
class Rest
{
  private $auth = null;
  private $messages = null;
  private $data = null;
  private $app = null;

  function __construct()
  {
    $this->app = new App();
  }

  public function CALL()
  {
    if(isset($_SERVER["PATH_INFO"]) && isset($_SERVER["REQUEST_METHOD"]))
    {
      $method = $_SERVER["REQUEST_METHOD"];

      $parameters = false;
      $postdata = file_get_contents("php://input");

      if($method === "POST")
      {
        if(isset($_POST) && !empty($_POST)) $parameters = $_POST;
        else $parameters = json_decode($postdata, true);
      }
      else if($method === "GET") $parameters = $_GET;

      $parameters["call"] = $this->getCall();
      if($parameters && is_array($parameters) && isset($parameters["call"]["function"]))
      {
        $return =  $this->app->$parameters["call"]["function"]($parameters);
        if(isset($return["data"]))
        {
          $data = $return["data"];
          unset($return["data"]);
        }
        else $data = array();

        $this->DATA($data, "data");
        $this->DATA($return, "system");

      }
      else $this->ERROR("Incorrect call!");
    }else $this->ERROR("Incorrect call!");
    header('Content-Type: application/json');
    header('Content-type: text/plain; charset=utf-8');
    $data = $this->data;
    $this->data = false;
    echo json_encode($data);
    //if($method === "POST") $this->BACK();
  }

  private function getCall()
  {
    $call = array();
    if(isset($_SERVER["PATH_INFO"]))
    {
      $tmp = explode("/", $_SERVER["PATH_INFO"]);
      if(is_array($tmp))
      {
        $key = 0;
        foreach ($tmp as $command) {
          $command = onlyLettersAndNumbers(trim($command));
          if(!empty($command))
          {
            if($key === 0) $call["function"] = $command;
            elseif($key === 1) $call["object"] = $command;
            elseif($key === 2) $call["id"] = $command;
            else $call[] = $command;
            ++$key;
          }

        }
      }
    }
    return $call;
  }
  private function BACK()
  {
    $location = (!empty($_SERVER["HTTP_REFERER"]))?$_SERVER["HTTP_REFERER"]:ADDRESS."index.html";
    header("Location: ".$location);
    die("Redirecting to: ".$location);
  }

  private function GETObject($parameters)
  {
    $data = false;
    $userid = $this->userID();
    if(isset($parameters["object"]) && isClass($parameters["object"]) && isset($parameters["id"]))
    {
      $objectName = onlyLetters($parameters["object"]);
      $table = classAndTable($objectName, "class");
      $ids = (is_array($parameters["id"]))?$parameters["id"]:array($parameters["id"]);
      $order = (isset($parameters["order"]))?$parameters["order"]:FALSE;
      {
        $data = SQL_SELECT(
          $_columns = FALSE,
          $_table=$table,
          $_joinTable = FALSE,
          $_joinTableId = FALSE,
          $_where = "id IN ('".implode("','", $ids)."') AND active = '1'",
          $_order = $order,
          $_offset = FALSE,
          $_limit = FALSE,
          $_object = FALSE,
          $_onlyFirstRow = FALSE,
          $_noIDKeys = TRUE
        );
      }
    }
    if($data)
    {
      $this->DATA($data, $objectName);
      $this->DATA(COUNT($data), $objectName."COUNT");
    }
    else $this->ERROR("Getting failed!");
  }

  private function EDITObject($parameters)
  {
    $success = false;
    if(isset($parameters["object"]) && isClass($parameters["object"]))
    {
      $this->DATA($parameters);
      $id = (isset($parameters["id"]))?$parameters["id"]:0;
      eval("\$object = new ".$parameters["object"]."(".$id.");");
      unset($parameters["object"]);
      if(is_object($object))
      {
        if(isset($parameters["columns"]) && is_array($parameters["columns"]))
        {
          foreach ($parameters["columns"] as $column => $value) $object->setValue($column, $value);
        }
        $success = $object->COMMIT();
      }
    }

    if($success) $this->MESSAGE("Editing successful!");
    else $this->ERROR("Editing failed!");
  }


  private function REMOVEObject($parameters)
  {
    $success = false;
    if(isset($parameters["object"]) && isClass($parameters["object"]) && isset($parameters["id"])
      && is_numeric($parameters["id"]) && $parameters["id"] > 0)
    {
      eval("\$object = new ".$parameters["object"]."(".$parameters["id"].");");
      if(is_object($object)) $success = $object->REMOVE();
    }

    if($success) $this->MESSAGE("Remove successful!");
    else $this->ERROR("Remove failed!");
  }

  private function LOGIN($parameters)
  {
    $success  = false;
    if(isset($parameters["username"]) && isset($parameters["password"]))
    {
      $success = (is_object($this->auth) && $this->auth->LOGIN($parameters["username"], $parameters["password"]));
    }

    if($success)
    {
      $this->MESSAGE("Logged in!");
      $this->DATA($this->auth->getUserData(), "user");
    }
    else
    {
      $this->ERROR("Login failed!");
      $this->DATA(false, "user");
    }
  }

  private function ISLOGEDIN($parameters)
  {
	$isLoged = $this->CHECK();
    $this->DATA($isLoged, $id = "ISLOGEDIN");
  	if($isLoged)
  	{
        $this->DATA($this->auth->getUserData(), "user");
  	}
  }

  private function LOGOUT($parameters)
  {

    $success = (is_object($this->auth) && $this->auth->LOGOUT());

    if($success) $this->MESSAGE("Logged in!");
    else $this->ERROR("Login failed!");
  }

  private function ERROR($text=false)
  {
    if(isset($text))$this->DATA(array($text=>$text), "ERROR");
    else $this->ERROR("No text set on ERROR call!");
  }
  private function MESSAGE($text)
  {
    if(isset($text))$this->DATA(array($text=>$text), "MESSAGE");
    else $this->ERROR("No text set on MESSAGE call!");
  }

  private function DATA($data, $id = false)
  {
    if($id)
    {
      if(isset($this->data[$id])) $this->data[$id] += $data;
      else $this->data[$id] = $data;
    }
    else $this->data[] = $data;
  }

  private function CHECK()
  {
    return (is_object($this->auth))?$this->auth->CHECK():false;
  }

  private function userID()
  {
    return (is_object($this->auth) && is_object($this->auth->user))?$this->auth->user->getID():false;
  }

  private function GetProducts()
  {
    $table = "product";
    $ids = SQL_SELECT(
      $_columns = "id",
      $_table=$table,
      $_joinTable = FALSE,
      $_joinTableId = FALSE,
      $_where = "active = '1'",
      $_order = FALSE,
      $_offset = FALSE,
      $_limit = FALSE,
      $_object = FALSE,
      $_onlyFirstRow = FALSE,
      $_noIDKeys = TRUE
    );
    $parameters = array(
      "object" => classAndTable($table, "table"),
      "id" => $ids,
    );
    $this->GETObject($parameters);
    $this->GetProductTypes();
  }
  private function GetProductTypes()
  {
    $table = "product_type";
    $ids = SQL_SELECT(
      $_columns = "id",
      $_table=$table,
      $_joinTable = FALSE,
      $_joinTableId = FALSE,
      $_where = "active = '1'",
      $_order = FALSE,
      $_offset = FALSE,
      $_limit = FALSE,
      $_object = FALSE,
      $_onlyFirstRow = FALSE,
      $_noIDKeys = TRUE
    );
    $parameters = array(
      "object" => classAndTable($table, "table"),
      "id" => $ids,
    );
    $this->GETObject($parameters);
  }
}
?>
