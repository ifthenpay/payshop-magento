<?php
/**
* Ifthenpay_Payshop module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payshop
* @author      Ifthenpay
* @copyright   Ifthenpay (https://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\Payshop\Helper\Callback\Validator;

use Ifthenpay\Payshop\Helper\Data;

class CallbackValidator extends Data
{

    private function checkIfAntiPhishingIsValid($ap)
    {
        return (
            $ap == $this->scopeConfig->getValue(
                self::IFTHENPAY_ANTIPHISHING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
    }
     

    private function checkOrderValid($callbackData, $order)
    {
        if ((int)$callbackData->idcliente !== intval(round($order->getIncrementId()))) {
            return ['error' => 'Encomenda inválida.'];
        }
        if (!$order) {
            return ['error' => 'Encomenda não encontrada.']; 
        }
                
        if ($order->getTotalDue() == 0) {
            return ["error" => "A encomenda já foi paga."];
        }

        if (round($callbackData->valor, 2) !== round($order->getBaseGrandTotal(), 2)) {
            return ['error' => 'Valor da encomenda não é válido.'];
        }

        return true;
    }

    private function checkPayshopOrderValid($callbackData, $payshopDatabaseOrder)
    {
        if ($callbackData->idtransacao !== $payshopDatabaseOrder['id_transacao']) {
            return ['error' => 'Id de transação é inválido.']; 
        }

        if ($callbackData === $payshopDatabaseOrder['referencia']) {
            return ['error' => 'Referência Payshop não encontrada.'];
        }

        return true;
    }

    private function checkEstado($estado)
    {
        if (strtolower($estado) !== 'pago') {
            return ['error' => 'Encomenda não está paga.'];
        }
        return true;
    }
    
    public function checkCallback($requestCallbackData, $order, $payshopOrderData)
    {
        $errorAntiPhsishing = $this->checkIfAntiPhishingIsValid($requestCallbackData->key);
        $errorOrder = $this->checkOrderValid($requestCallbackData, $order); 
        $errorPayshopOrder = $this->checkPayshopOrderValid($requestCallbackData, $payshopOrderData);
        $errorEstado = $this->checkEstado($requestCallbackData->estado);
        
        if ($errorAntiPhsishing['error']) {
            return $errorAntiPhsishing;
        } else if ($errorOrder['error']) {
            return $errorOrder;
        } else if ($errorPayshopOrder['error']) {
            return $errorPayshopOrder;
        } else if ($errorEstado['error']) {
            return $errorEstado;
        } else {
            return true;
        }
    }       
}
