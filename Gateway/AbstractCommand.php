<?php

namespace FatZebra\Gateway\Gateway;

abstract class AbstractCommand implements \Magento\Payment\Gateway\CommandInterface
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var \FatZebra\Gateway\Helper\Data */
    protected $fatzebraHelper;

    /** @var \FatZebra\Gateway\Model\GatewayFactory */
    protected $gatewayFactory;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * AbstractCommand constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \FatZebra\Gateway\Helper\Data $fatzebraHelper
     * @param \FatZebra\Gateway\Model\GatewayFactory $gatewayFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \FatZebra\Gateway\Helper\Data $fatzebraHelper,
        \FatZebra\Gateway\Model\GatewayFactory $gatewayFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->fatzebraHelper = $fatzebraHelper;
        $this->gatewayFactory = $gatewayFactory;
        $this->logger = $logger;
    }

    /**
     * @param $storeId
     * @return \FatZebra\Gateway\Model\Gateway
     */
    public function getGateway($storeId)
    {
        $username = $this->scopeConfig->getValue(
            \FatZebra\Gateway\Helper\Data::CONFIG_PATH_FATZEBRA_USERNAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $token = $this->scopeConfig->getValue(
            \FatZebra\Gateway\Helper\Data::CONFIG_PATH_FATZEBRA_TOKEN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $sandbox = (bool)$this->scopeConfig->getValue(
            \FatZebra\Gateway\Helper\Data::CONFIG_PATH_FATZEBRA_SANDBOX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->gatewayFactory->create([
            'username' => $username,
            'token' => $token,
            'test_mode' => $sandbox
        ]);
    }
}