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

/**
 * Renderer for Payments Advanced information
 */
namespace Ifthenpay\Payshop\Block\Adminhtml\System\Config\Callback;

use Ifthenpay\Payshop\Helper\Data;
use Ifthenpay\Payshop\Helper\Html\PayshopHtml;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class Instrucoes extends Field
{
    private $ifthenpayPayshopHelperData;
    private $ifthenpayPayshopHelperPayshopHtml;

    /**
     * Template path
     *
     * @var string
     */
    public $_template = 'system/config/callback/instrucoes.phtml';

    public function __construct(
        Context $context,
        Data $ifthenpayPayshopHelperData,
        PayshopHtml $ifthenpayPayshopHelperPayshopHtml,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ifthenpayPayshopHelperData = $ifthenpayPayshopHelperData;
        $this->ifthenpayPayshopHelperPayshopHtml = $ifthenpayPayshopHelperPayshopHtml;
    }


    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $columns = $this->getRequest()->getParam('website') || $this->getRequest()->getParam('store') ? 5 : 4;
        return $this->_decorateRowHtml($element, "<td colspan='{$columns}'>" . $this->toHtml() . '</td>');
    }

    public function renderCallbackInfo()
    {
        return $this->ifthenpayPayshopHelperPayshopHtml->renderCallbackInfo(
            $this->_storeManager->getStore()->getBaseUrl(), $this->getAntiPhishingKey()
        );
    }

    public function getPayshopKey()
    {
        return $this->ifthenpayPayshopHelperData->getPayshopKey();
    }

    public function getAntiPhishingKey()
    {
        return $this->ifthenpayPayshopHelperData->getAntiPhishing();
    }
}
