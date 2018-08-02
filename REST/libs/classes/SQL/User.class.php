<?php
class User extends SQLClass
{
	private $pass = FALSE;
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="username", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="password", $type=false, $_value="", $_size=255, $_empty=false);
		$this->addColumn($_name="userGroup", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="firstName", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="lastName", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="address", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="postcode", $type=false, $_value="", $_size=5, $_empty=false);
		$this->addColumn($_name="city", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="country", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="email", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="data", $type="TXT");
		$this->addColumn($_name="active", $type=false, $_value=1, $_size=1, $_empty=true);
		$this->_table = "user";
	}

	protected function BeforeCOMMIT()
	{
		if(empty($this->getValue("password")))
		{
			$this->pass = genRandStr(8);
			$this->setValue("password",passwordHash($this->pass));
		}
		return true;
	}

	protected function AfterCOMMIT()
	{
		if($this->pass)
		{
			$body =
			"
			Tervetuloa ".$this->getValue("firstName")." ". $this->getValue("lastName")." verkkokauppaamme.

			Käyttäjänimi: " . $this->getValue("username") . "
			Salasana: " . $this->pass . "
			Osoite: " . ADDRESS . "

			Jos et tehnyt tälläistä tunnusta ota yhteyttä: " . EMAIL . "";
			$title = NAME.": Tervetuloa";
			mail($this->getValue("email"),$title,$body);
		}
		return true;
	}

	public function checkPassword($_password)
	{

		if($this->inDataspace())
		{
			return checkPassword($_password ,$this->getValue("password"));
		}else return FALSE;
	}
}
?>
