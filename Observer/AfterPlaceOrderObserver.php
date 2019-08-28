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

namespace Ifthenpay\Payshop\Observer;

use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Sales\Model\Order;
use Magento\Framework\Event\Observer;

class AfterPlaceOrderObserver extends AbstractDataAssignObserver
{
    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;

    public function __construct(
        Order $order
    )
    {
        $this->order = $order;
    }

    public function execute(Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrderIds();
        $currentOrder = $this->order->load($orderId);
        $currentState = $currentOrder->getState();
        $save = false;
        
        if ($currentState !== $currentOrder::STATE_NEW) {
            $currentOrder->setState($currentOrder::STATE_PENDING_PAYMENT);
            $currentOrder->setStatus('pending');
            $save = true;
        }
        if ($save) {
            $currentOrder->save();
        }
    }
}
