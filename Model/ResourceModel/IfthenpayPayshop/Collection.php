<?php

namespace Ifthenpay\Payshop\Model\ResourceModel\IfthenpayPayshop;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Ifthenpay\Payshop\Model\IfthenpayPayshop', 'Ifthenpay\Payshop\Model\ResourceModel\IfthenpayPayshop');
	}

}