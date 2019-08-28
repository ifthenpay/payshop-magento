<?php
/**
* Ifthenpay_Payshop
*
* @package     Ifthenpay_Payshop
* @author      Ifthenpay
* @copyright   Ifthenpay
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*
* Ifthenpay_Payshop CreateInvoiceService
*
*/

namespace Ifthenpay\Payshop\Model\Service;

use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;

/**
 * Service responsible for creating new invoices for orders
 */
class CreateInvoiceService
{
    /**
     * @var Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;
    protected $invoiceSender;

    /**
     * @var Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * @param Magento\Sales\Model\Service\InvoiceService    $invoiceService
     * @param Magento\Framework\DB\Transaction              $transaction
     *
     * @return void
     */
    public function __construct(
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        Transaction $transaction
    ) {
        $this->invoiceService = $invoiceService;
        $this->invoiceSender = $invoiceSender;
        $this->transaction = $transaction;
    }

    /**
     * Creates an invoice for a given order
     *
     * @param   Magento\Sales\Model\Order   $order
     *
     * @return boolean
     */
    public function createInvoice(Order $order)
    {
        if(!$order->getId()) {
            return false;
        }

        if($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->setState(Invoice::STATE_PAID);
            $invoice->register();
            $invoice->save();

            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );

            $transactionSave->save();

            $this->invoiceSender->send($invoice);
            //send notification code
            $order->addStatusHistoryComment(
                __('Notified customer about invoice #%1.', $invoice->getId())
            )
            ->setIsCustomerNotified(true)
            ->save();
        }

        return true;
    }
}
