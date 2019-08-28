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

namespace Ifthenpay\Payshop\Block;

use Ifthenpay\Payshop\Helper\Data;
use Magento\Payment\Block\Info as BlockInfo;
use Magento\Framework\View\Element\Template\Context;

class Info extends BlockInfo
{
    private $ifthenpayPayshopHelperData;

    public function __construct(Context $context, Data $ifthenpayPayshopHelperData, array $data = [])
    {
        $this->validator = $context->getValidator();
        $this->resolver = $context->getResolver();
        $this->_filesystem = $context->getFilesystem();
        $this->templateEnginePool = $context->getEnginePool();
        $this->_storeManager = $context->getStoreManager();
        $this->_appState = $context->getAppState();
        $this->templateContext = $this;
        $this->pageConfig = $context->getPageConfig();
        $this->ifthenpayPayshopHelperData = $ifthenpayPayshopHelperData;
        parent::__construct($context, $data);
    }

    public function getSpecificInformation()
    {   
        $informations['ID Pedido'] = $this->getInfo()->getAdditionalInformation('ID Pedido');
        $informations['Referencia Payshop'] = $this->ifthenpayPayshopHelperData->formatReferencia($this->getInfo()->getAdditionalInformation('Referencia Payshop'));
        $informations['Validade'] = $this->ifthenpayPayshopHelperData->convertValidade($this->getInfo()->getAdditionalInformation('Validade')); 
        $informations['Erro'] = $this->getInfo()->getAdditionalInformation('Erro');
        return (object)$informations;
    }

    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }
}
