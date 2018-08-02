<?php
  function getUserNames()
  {
    $data = array();
    $col = "CONCAT(firstName, ' ' , lastName)";
    $query = "SELECT id, ".$col." FROM user";
    $result = SQL_SEND_QUERY($query);
    if(is_object($result))
    {
      while($row = $result->fetch_assoc())
			{
        if(isset($row["id"]) && isset($row[$col])) $data[$row["id"]] = $row[$col];
      }
    }

    return $data;
  }
 ?>
