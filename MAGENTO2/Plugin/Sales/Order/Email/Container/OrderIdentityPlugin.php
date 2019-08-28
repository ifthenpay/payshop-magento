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

namespace Ifthenpay\Payshop\Plugin\Sales\Order\Email\Container;

use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;

class OrderIdentityPlugin
{
    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        Session $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Sales\Model\Order\Email\Container\OrderIdentity $subject
     * @param callable $proceed
     * @return bool
     */
    public function aroundIsEnabled(OrderIdentity $subject, callable $proceed)
    {
        $returnValue = $proceed();

        $forceOrderMailSentOnSuccess = $this->checkoutSession->getForceOrderMailSentOnSuccess();
        if (isset($forceOrderMailSentOnSuccess) && $forceOrderMailSentOnSuccess) {
            
            if ($returnValue) {
                $returnValue = false;
            } else {
                $returnValue = true;
            }
    
            $this->checkoutSession->unsForceOrderMailSentOnSuccess();
        }
        return $returnValue;
    }
}