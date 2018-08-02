<?php
class Order extends SQLClass
{
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id", $type="id");
		$this->addColumn($_name="user_id", $type="id_empty");
		$this->addColumn($_name="product_ids", $type="TXT");
		$this->addColumn($_name="shipping_id", $type=false, $_value=1, $_size=10, $_empty=false);
		$this->addColumn($_name="price", $type=false, $_value=0.00, $_size=10, $_empty=true);
		$this->addColumn($_name="shipped", $type="datetime");
		$this->addColumn($_name="firstName", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="lastName", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="address", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="postcode", $type=false, $_value="", $_size=5, $_empty=false);
		$this->addColumn($_name="city", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="country", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="email", $type=false, $_value="", $_size=64, $_empty=false);
		$this->addColumn($_name="created", $type="datetime");
		$this->addColumn($_name="active", $type=false, $_value=1, $_size=1, $_empty=true);
		$this->_table = "order";
	}

	protected function BeforeCOMMIT()
	{
		if(!$this->isOld())
		{
			$this->countPrice();
			$this->setValue("created", mysqlDateTime());
		}
		return true;
	}

	function countPrice()
	{
		$price = 0.00;
		$products = $this->getProducts("object");
		if(is_array($products) && !empty($products))
		{
			foreach ($products as $product) {
				if(is_object($product))
				{
					$pri = $product->getValue("price");
					if(is_numeric($pri)) $price += $pri;
				}
			}
		}
		$shipping = $this->getValue("shipping_id");
		if(is_numeric($shipping) && $shipping >0)
		{
			$extra = SQL_SELECT_VALUE_ID("shipping", "price", $shipping);
			if(is_numeric($shipping)) $price += $extra;
		}
		$this->setValue("price", $price);
	}

	function setShipped()
	{
		$this->setValue("shipped", mysqlDateTime());
	}

	function isShipped()
	{
		$shi = $this->getValue("shipped");
		return (isset($shi) && mysqlDateIsSet($shi));
	}

	function addProduct($id)
	{
		if(is_object($id)) $id = $id->getID();
		if(isset($id) && is_numeric($id) && $id > 0 && SQL_IS_ACTIVE($table="product", $id))
		{
			$products = $this->getProducts();
			$products[] = $id;
			return $this->setProducts($products);
		}
		return FALSE;
	}

	function removeProduct($id)
	{
		$products = $this->getProducts();
		if(isset($id) && is_numeric($id) && is_array($products) && !empty($products))
		{
			foreach ($products as $key => $value) {
				if($id == $value)
				{
					unset($products[$key]);
					$this->setProducts($products);
					return true;
				}
			}
		}
		return false;
	}

	function getProducts($type = false)
	{
		$produts = array();
		$product_ids = $this->getValue("product_ids");
		if($product_ids && !empty($product_ids))
		{
			$product_ids = explode(";", $product_ids);
			if(!empty($product_ids) && is_array($product_ids))
			{
				if($type)
				{
					foreach ($product_ids as $id) {
						$tmp = new Product($id);
						if($type == "object") $produts[] = $tmp;
						elseif($type == "array") $produts[] = $tmp->getValues();
					}
				}
				else $produts = $product_ids;

			}
		}
		return $produts;
	}

	function setProducts($products)
	{
		if(isset($products) && is_array($products))
		{
			$product_ids = array();
			if(!empty($products))
			{
				foreach ($products as $id) {
					if(is_object($id)) $id = $id->getID();
					if(is_numeric($id) && $id > 0) $product_ids[] = $id;
				}
			}
			if(is_array($product_ids))
			{
				$product_ids = implode(";", $product_ids);
				$this->setValue("product_ids", $product_ids);
				$this->countPrice();
				return true;
			}
		}

		return false;
	}

}
?>
