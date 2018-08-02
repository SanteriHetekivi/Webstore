<?php
  set_time_limit(60*5); //5min

  error_reporting(-1);
  ini_set('display_errors', 'On');
  require_once("conf.php");
  require_once(LIBS_PATH."dump_r/dump_r.php");

  function debug($variable)
  {
    dump_r($variable);
  }

  require_once(LIBS_PATH."mysql.php");
  require_once(LIBS_PATH."functions.php");
  require_once(LIBS_PATH."mysqlFunctions.php");
  require_once(LIBS_PATH."classes.php");



  session_name("HETEKIVI");
  session_start();

  if(!isset($_SESSION["rest"])) $_SESSION["rest"]=new Rest();
  $Rest = &$_SESSION["rest"];

  $Rest->CALL();

?>
