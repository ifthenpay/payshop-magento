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

namespace Ifthenpay\Payshop\Controller\Callback;

use \Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\Controller\Result\JsonFactory;
use Ifthenpay\Payshop\Helper\Data;
use Ifthenpay\Payshop\Helper\Callback\Validator\CallbackValidator;
use Ifthenpay\Payshop\Model\IfthenpayPayshopFactory;
use Ifthenpay\Payshop\Model\Service\CreateInvoiceService;


class Check extends Action
{
    private $createInvoiceService;
    private $orderFactory;
    private $ifthenpayPayshopHelperData;
    private $ifthenpayPayshopHelperCallbackValidator;
    private $ifthenpayPayshopFactory;
    private $resultJsonFactory;

    public function __construct(
        Context $context,
        CreateInvoiceService $createInvoiceService,
        OrderFactory $orderFactory,
        Data $ifthenpayPayshopHelperData,
        CallbackValidator $ifthenpayPayshopHelperCallbackValidator,
        IfthenpayPayshopFactory $ifthenpayPayshopFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->ifthenpayPayshopHelperData = $ifthenpayPayshopHelperData;
        $this->ifthenpayPayshopHelperCallbackValidator = $ifthenpayPayshopHelperCallbackValidator;
        $this->createInvoiceService = $createInvoiceService;
        $this->ifthenpayPayshopFactory = $ifthenpayPayshopFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
       
    }


    public function execute()
    {

        $callbackResult = $this->callbackExecute();
        $jsonResponse = $this->resultJsonFactory->create()->setData($callbackResult);
        
        if (!isset($callbackResult)) {
            $jsonResponse->setHttpResponseCode(403);
        }
        
        return $jsonResponse;
    }

    private function callbackExecute()
    {

        $callbackRequest = (object)$this->getRequest()->getParams();

        $order = $this->orderFactory->create()->loadByIncrementId((int)$callbackRequest->idcliente);            
        $order_id = $order->getId();

        if (!$order_id) {
            return ['error' => 'Id da encomenda não é válido.'];
        }
        
        $order_status = $order->getStatus();

        if ($order_status === Order::STATE_PROCESSING) {
            return ['error' => 'Estado da encomenda: ' . Order::STATE_PROCESSING . '.'];
        }
        
        $order_value = $order->getGrandTotal();

        $ifthenpayPayshopModel = $this->ifthenpayPayshopFactory->create();
        $payshopOrderData = $ifthenpayPayshopModel->load($order->getIncrementId(), 'order_id')->getData();
        
        $callbackCheck = $this->ifthenpayPayshopHelperCallbackValidator->checkCallback($callbackRequest, $order, $payshopOrderData);

        if (!$callbackCheck['error']) {
            
            $this->createInvoiceService->createInvoice($order);

            $order->setState(Order::STATE_PROCESSING)
                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
            $order->setTotalPaid($order_value);
            $order->getPayment()->setIsTransactionClosed(true);
            $order->save();
    
            return ["success" => true, "message" => "Pagamento foi concluido com sucesso."];
        
        } else {
            return $callbackCheck;
        }
    }
    
    private function capture($order)
    {
        $payment = $order->getPayment();
        try {
            $payment->capture();
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
        $order->save();
        return ["success" => true, "message" => "Pagamento foi capturado com sucesso."];
    }
}