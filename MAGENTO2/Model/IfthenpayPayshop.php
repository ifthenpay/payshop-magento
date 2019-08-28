<?php

namespace Ifthenpay\Payshop\Model;

use Magento\Framework\Model\AbstractModel;

class IfthenpayPayshop extends AbstractModel
{
	
	protected function _construct()
	{
		$this->_init('Ifthenpay\Payshop\Model\ResourceModel\IfthenpayPayshop');
	}
}