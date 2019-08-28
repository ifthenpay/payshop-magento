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

namespace Ifthenpay\Payshop\Block\Checkout\Onepage\Success;

use Ifthenpay\Payshop\Helper\Html\PayshopHtml;
use \Ifthenpay\Payshop\Model\Ui\ConfigProvider;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\Config;
use Magento\Framework\App\Http\Context as HttpContest;
use Magento\Checkout\Block\Onepage\Success;

Class Response extends Success
{
    private $ifthenpayPayshopHelperPayshopHtml;
    /**
     * Prepares block data
     *
     * @return void
     */
    protected $order;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        Config $orderConfig,
        HttpContest $httpContext,
        PayshopHtml $ifthenpayPayshopHelperPayshopHtml,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        $this->ifthenpayPayshopHelperPayshopHtml = $ifthenpayPayshopHelperPayshopHtml;
    }

    protected function _construct()
    {
        $this->setModuleName('Magento_Checkout');
        parent::_construct();
    }

    

    protected function prepareBlockData()
    {
        $this->order = $this->_checkoutSession->getLastRealOrder();
    }

    public function renderPayshopTable()
    {
        return $this->ifthenpayPayshopHelperPayshopHtml->renderPayshopPaymentTable(
            $this->order->getPayment()->getAdditionalInformation('Referencia Payshop'),
            $this->getFormatValue(), $this->order->getPayment()->getAdditionalInformation('Validade'));
    }

    private function getFormatValue()
    {
        return number_format($this->order->getGrandTotal(), '2', '.', ',');
    }

    public function isMethodIfthenpay()
    {
        if ($this->order->getPayment()->getMethod() == ConfigProvider::CODE) {
            return true;
        }
        return false;
    }
}