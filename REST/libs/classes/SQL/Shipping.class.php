<?php
class Shipping extends SQLClass
{
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="title");
		$this->addColumn($_name="price", $type=false, $_value=0.00, $_size=10, $_empty=true);
		$this->addColumn($_name="active", $type=false, $_value=1, $_size=1, $_empty=true);
		$this->_table = "shipping";
	}
}
?>
