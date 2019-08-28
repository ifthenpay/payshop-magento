<?php

/**
* Ifthenpay_Payshop module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payshop
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\Payshop\Model\Adminhtml\Source;

use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class PaymentAction
 */
class PaymentAction extends AbstractMethod {

    protected $_isInitializeNeeded = true;

    /**
     * {@inheritdoc}
     */
    public function getConfigPaymentAction() {
        return ($this->getConfigData('order_status') == 'pending') ? null : parent::getConfigPaymentAction();
    }

}
