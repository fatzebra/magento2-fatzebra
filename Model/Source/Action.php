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

class Action
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'auth_capture',
                'label' => 'Authorize and Capture'
            ),
            array(
                'value' => 'auth',
                'label' => 'Authorize Only'
            )
        );
    }
}
