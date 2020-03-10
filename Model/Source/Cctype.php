<?php
/**
 * Payment CC Types Source Model
 *
 * @category    Fat Zebra
 * @package     FatZebra_Gateway
 * @copyright   Fat Zebra (https://www.fatzebra.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace FatZebra\Gateway\Model\Source;

class Cctype extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'AE', 'JCB');
    }
}
