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

namespace Ifthenpay\Payshop\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\OrderFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const IFTHENPAY_PAYSHOPKEY = 'payment/ifthenpay_payshop/payshop_key'; 	 
    const IFTHENPAY_ANTIPHISHING = 'payment/ifthenpay_payshop/chave_anti_phishing';
    
    private static $api = 'https://ifthenpay.com/api/payshop/get';

    public $_configTable;
    public $connection;
    public $_orderTable;
    public $_orderFactory;
 
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource,
        OrderFactory $orderFactory
    ) {
        $this->_configTable = $resource->getTableName('core_config_data');
        $this->_orderTable = $resource->getTableName('sales_order');
        $this->_orderFactory = $orderFactory;
        $this->connection = $resource->getConnection();

        parent::__construct($context);
    }

    public function getPayshopKey()
    {
        return $this->scopeConfig->getValue(
            self::IFTHENPAY_PAYSHOPKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAntiPhishing()
    {
        $chaveap = $this->scopeConfig->getValue(
            self::IFTHENPAY_ANTIPHISHING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($chaveap == "" || $chaveap == null) {
            $chaveap=md5(time());

            $bindValues = ['path' => self::IFTHENPAY_ANTIPHISHING ];
            $select = $this->connection->select()->from($this->_configTable)->where('path = :path');
            $exists = $this->connection->fetchOne($select, $bindValues);

            $bind = ['value' => $chaveap];

            if ($exists) {
                $this->connection->update($this->_configTable, $bind, ['path=?' => self::IFTHENPAY_ANTIPHISHING]);
            } else {
                $bind['path'] = self::IFTHENPAY_ANTIPHISHING;
                $bind['value'] = $chaveap;
                $this->connection->insert($this->_configTable, $bind);
            }
        }

        return $chaveap;
    }

    public function makeValidade($validade) {
        return (new \DateTime(date("Ymd")))->modify('+' . $validade . 'day')
          ->format('Ymd');
    }

    public function convertValidade($validade)
    {
        if ($validade === '0') {
          return 'Sem data limite';
        } else {
          return date('d-m-Y', strtotime($validade));
        }
        
    }

    public function formatReferencia($referencia)
    {
        return substr($referencia, 0, 3) . ' ' . substr($referencia, 3, 3) . ' ' . substr($referencia, 6, 3) . ' ' . substr($referencia, 9, 4);
    }
    
    public function makePayment($payshopKey, $order_id, $valor, $validade) 
    {          
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => self::$api . '?payshopkey=' . $payshopKey . '&id=' . $order_id . '&valor=' . $valor . '&validade=' . $validade,
          CURLOPT_USERAGENT => 'Ifthenpay Payshop Client'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        return json_decode($resp);
    }

    public function validation($key, $validade)
	{
	    if (!$key) {
            return 'Payshop key é obrigatória';
        } else if (strlen($key) !== 10) {
            return 'Payshop key não é válida.';
        } 
        if ($validade === '') {
          return 'É obrigatório definir uma validade, se não pretender validade coloque 0';
      }
	}
}