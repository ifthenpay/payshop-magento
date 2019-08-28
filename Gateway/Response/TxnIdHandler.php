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

namespace Ifthenpay\Payshop\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use \Ifthenpay\Payshop\Model\IfthenpayPayshopFactory;

class TxnIdHandler implements HandlerInterface
{
    protected $ifthenpayPayshopFactory;

    public function __construct(IfthenpayPayshopFactory $ifthenpayPayshopFactory)
    {
        $this->ifthenpayPayshopFactory = $ifthenpayPayshopFactory;
    }

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */

    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();

        if ($response['id_transacao']) {
            $payment->setAdditionalInformation('ID Pedido', $response['id_transacao']);
            $payment->setAdditionalInformation('Referencia Payshop', $response['referencia']);
            $payment->setAdditionalInformation('Validade', $response['validade']);
            $payment->setTransactionId($payment->getAdditionalInformation('ID Pedido'));
            $this->insertDatabase($response);
            $payment->setIsTransactionPending(false);   
        } else {
            $msg = $response['code'] . ' ' . $response['error'];
            $payment->setAdditionalInformation('Erro', $msg);
            $this->insertDatabase($response);
            $payment->setIsTransactionClosed(true);
        }
    }

    private function insertDatabase($data)
    {
        unset($data['code']);
        $ifthenpayPayshopModel = $this->ifthenpayPayshopFactory->create();
        $ifthenpayPayshopModel->setData($data);
        $ifthenpayPayshopModel->save();
    }
    
}
