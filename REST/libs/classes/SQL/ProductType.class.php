<?php
class ProductType extends SQLClass
{
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="title");
		$this->addColumn($_name="active", $type=false, $_value=1, $_size=1, $_empty=true);
		$this->_table = "product_type";
	}
}
?>
