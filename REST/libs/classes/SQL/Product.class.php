<?php
class Product extends SQLClass
{
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="title");
		$this->addColumn($_name="product_type_id", $type="id");
		$this->addColumn($_name="price", $type=false, $_value=0.00, $_size=10, $_empty=true);
		$this->addColumn($_name="image", $type="url");
		$this->addColumn($_name="active", $type=false, $_value=1, $_size=1, $_empty=true);
		$this->_table = "product";
	}

	public function getTypeTitle()
	{
		return SQL_SELECT_VALUE_ID($_table="product_type", $_column="title", $_id=$this->getValue("product_type_id"));
	}
}
?>
