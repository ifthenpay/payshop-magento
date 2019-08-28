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


namespace Ifthenpay\Payshop\Gateway\Http\Client;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Webapi\Soap\ClientFactory;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Ifthenpay\Payshop\Model\IfthenpayPayshopFactory;
use Ifthenpay\Payshop\Helper\Data;

/**
 * Class Soap
 * @package Magento\Payment\Gateway\Http\Client
 * @api
 */
class Client implements ClientInterface
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ConverterInterface | null
     */
    private $converter;
    /**
     * @var ClientFactory
     */
    private $clientFactory;
    private $ifthenpayPayshopFactory;
    private $ifthenpayPayshopHelperData;


    protected $_messageManager;


    /**
     * @param Logger $logger
     * @param ClientFactory $clientFactory
     * @param ConverterInterface | null $converter
     */
    public function __construct(
        ManagerInterface $messageManager,
        Logger $logger, 
        ClientFactory $clientFactory,
        IfthenpayPayshopFactory $ifthenpayPayshopFactory,
        Data $ifthenpayPayshopHelperData,
        ConverterInterface $converter = null
    )
    {
        $this->logger = $logger;
        $this->converter = $converter;
        $this->clientFactory = $clientFactory;
        $this->_messageManager = $messageManager;
        $this->ifthenpayPayshopFactory = $ifthenpayPayshopFactory;
        $this->ifthenpayPayshopHelperData = $ifthenpayPayshopHelperData;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     * @throws \Exception
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $order_id = $transferObject->getBody()['order_id'];
        $ifthenpayPayshopModel = $this->ifthenpayPayshopFactory->create();
        $order = $ifthenpayPayshopModel->load($order_id, 'order_id');
        $result = null;

        if (empty($order->getData()) && is_null($order->getId())) {
            try {
                $validade = $this->ifthenpayPayshopHelperData->makeValidade($transferObject->getBody()['payshopValidade']);
                $result = $this->ifthenpayPayshopHelperData->makePayment(
                    $transferObject->getBody()['payshopKey'],
                    $transferObject->getBody()['order_id'],
                    $transferObject->getBody()['valor'],
                    $validade);

                if ($result->Code === '0') {
                    $msg = 'Pagamento por Payshop Concluído com sucesso. Efectue o pagamento num agente Payshop.';
                    $this->_messageManager->addSuccessMessage(__($msg));
                } else {
                    $this->_messageManager->addWarningMessage(__('Ocorreu um erro por favor contacte o dono da loja.'));
                }
    
            } catch (\Exception $e) {
                throw $e->getMessage();
            }
        }
        return [
            'code' => $result->Code,
            'id_transacao' => $result->RequestId,
            'referencia' => $result->Reference,
            'validade' => $validade,
            'order_id' => $transferObject->getBody()['order_id'],
            'error' => $result->Message
        ];
    }
}
