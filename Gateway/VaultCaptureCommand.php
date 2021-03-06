<?php
/**
 * Vault capture command
 *
 * @category    Fat Zebra
 * @package     FatZebra_Gateway
 * @copyright   Fat Zebra (https://www.fatzebra.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace FatZebra\Gateway\Gateway;

use Psr\Log\LoggerInterface;

class VaultCaptureCommand extends AbstractCommand
{

    /**
     * @var \Magento\Vault\Api\PaymentTokenManagementInterface
     */
    private $tokenManagement;

    /**
     * VaultCaptureCommand constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \FatZebra\Gateway\Helper\Data $fatzebraHelper
     * @param \FatZebra\Gateway\Model\GatewayFactory $gatewayFactory
     * @param LoggerInterface $logger
     * @param \Magento\Vault\Api\PaymentTokenManagementInterface $tokenManagement
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \FatZebra\Gateway\Helper\Data $fatzebraHelper,
        \FatZebra\Gateway\Model\GatewayFactory $gatewayFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Vault\Api\PaymentTokenManagementInterface $tokenManagement
    ) {
        parent::__construct($scopeConfig, $fatzebraHelper, $gatewayFactory, $logger);
        $this->tokenManagement = $tokenManagement;
    }

    /**
     * Perform a purchase against a saved card token.
     * @param array $commandSubject
     * @return void
     * @throws \Magento\Payment\Gateway\Command\CommandException
     * @throws \Zend\Http\Client\Adapter\Exception\TimeoutException
     */
    public function execute(array $commandSubject)
    {
        /** @var  \Magento\Sales\Model\Order\Payment $payment */
        $payment = $commandSubject['payment']->getPayment();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        $storeId = $order->getStoreId();

        $publicHash = $payment->getAdditionalInformation('public_hash');
        $customerId = $payment->getAdditionalInformation('customer_id');

        $token = $this->tokenManagement->getByPublicHash(
            $publicHash,
            $customerId
        );

        if ($token) {
            /** @var  \FatZebra\Gateway\Model\Gateway $gateway */
            $gateway = $this->getGateway($storeId);
            $fraudData = $this->fatzebraHelper->buildFraudPayload($order);

            $result = $gateway->token_purchase(
                $token->getGatewayToken(),
                $commandSubject['amount'],
                $this->fatzebraHelper->getOrderReference($order),
                null,
                $fraudData
            );

            if ($result && isset($result['response']) && $result['response']['successful'] === true) {
                $payment->setLastTransId($result['response']['transaction_id']);
            } else {
                $errors = isset($result['errors']) ? $result['errors'] : ['Gateway error'];
                $this->logger->critical(__(
                    'Vault payment error (Order #%1): %2',
                    $order->getIncrementId(),
                    implode('. ', $errors)
                ));
                throw new \Magento\Payment\Gateway\Command\CommandException(__('Payment failed, please contact customer service.'));
            }
        } else {
            $this->logger->critical(__(
                "Unable to load token. Customer ID: %1. Public hash: %2",
                $customerId,
                $publicHash
            ));
            throw new \Magento\Payment\Gateway\Command\CommandException(__('Unable to place order.'));
        }
    }
}
