<?php

namespace Ifthenpay\Payshop\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class IfthenpayPayshop extends AbstractDb
{
		
	protected function _construct()
	{
		$this->_init('ifthenpay_payshop', 'id');
	}
	
}