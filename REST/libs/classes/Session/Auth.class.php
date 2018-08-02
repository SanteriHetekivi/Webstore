<?php

  /**
   *
   */
  class Auth
  {
    public $user = false;
    private $data = false;

    function __construct()
    {
      $this->isLogged = false;
      $this->user = false;
      $this->data = false;
    }

    public function LOGIN($username, $password)
    {
      $userid = SQL_GET_ID($_table="user", $_where="username='".$username."'");
      if(is_numeric($userid))
  		{
  			$mUser = new User($userid);
  			if($mUser->checkPassword($password))
  			{
  				$this->user = $mUser;
  				$this->data = json_decode($mUser->getValue("data"));
  			}else return FALSE;
  		}else return FALSE;
		  return TRUE;
    }

    public function LOGOUT()
    {
      $this->isLogged = false;
      $this->user = false;
      $this->data = false;
      return TRUE;
    }

    public function CHECK($groups = false)
    {
      $user = $this->user;
      $group = (!$groups || ( is_array($groups) && in_array($user->getValue("group"), $groups) || $groups = $user->getValue("group")));
      return (isset($user) && $user && is_object($user) && $user->getID() > 0 && $group );
    }

    public function GET_DATA($key)
    {
      if(is_array($this->data) && isset($this->data[$key])) return $this->data[$key];
      else return false;
    }

    public function SET_DATA($key, $data)
    {
      if($this->CHECK())
      {
        if($this->data && is_array($this->data))
        {
          $this->data[$key] = $data;
        }
        else {
          $this->data = array($key => $data);
        }
        $this->user->setValue("data", json_encode($this->data));
        return $this->user->COMMIT();
      }
      return false;
    }

	public function getUserData()
	{
		$data =  Array();
		$user = $this->user;
		if(is_object($user))
		{
			$data = $user->getValues();
			unset($data["password"]);
		}
		return $data;
	}

  public function getUserGroup()
  {
    $user = $this->getUserData();
    if(isset($user["userGroup"])) return $user["userGroup"];
    else return false;
  }

  public function getUserID()
  {
    $user = $this->getUserData();
    if(isset($user["id"])) return $user["id"];
    else return false;
  }

  public function checkPage($page)
  {
    $group = $this->getUserGroup();
    if(!$group) $group = "guest";
    $pages =
    [
      "store"             =>["guest", "customer", "worker", "manager", "admin"],
      "user"              =>["customer", "worker", "manager", "admin"],
      "cart"              =>["guest", "customer", "worker", "manager", "admin"],
      "systemProducts"    =>["worker", "manager", "admin"],
      "systemProductTypes"=>["worker", "manager", "admin"],
      "systemShipping"    =>["worker", "manager", "admin"],
      "systemOrders"      =>["worker", "manager", "admin"],
      "systemUsers"       =>["manager", "admin"]
    ];

    $groups = (isset($pages[$page])) ? $pages[$page] : [];
    $success = in_array($group,$groups);

    return $success;
  }

}

?>
