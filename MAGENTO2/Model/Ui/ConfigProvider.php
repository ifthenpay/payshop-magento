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

namespace Ifthenpay\Payshop\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'ifthenpay_payshop';

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
       return [
            'payment' => [
                self::CODE => [
                    'phoneNumber' => '954560836'
                ]
            ]
        ];
    }
}
