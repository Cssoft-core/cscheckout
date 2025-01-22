<?php

namespace CSSoft\Cscheckout\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    protected $helper;

    /**
     * @var \CSSoft\Cscheckout\Helper\Config\Payment
     */
    protected $paymentHelper;

    /**
     * @var \CSSoft\Cscheckout\Helper\Config\Shipping
     */
    protected $shippingHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \CSSoft\Cscheckout\Helper\Data $helper
     * @param \CSSoft\Cscheckout\Helper\Config\Payment $paymentHelper
     * @param \CSSoft\Cscheckout\Helper\Config\Shipping $shippingHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \CSSoft\Cscheckout\Helper\Data $helper,
        \CSSoft\Cscheckout\Helper\Config\Payment $paymentHelper,
        \CSSoft\Cscheckout\Helper\Config\Shipping $shippingHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->paymentHelper = $paymentHelper;
        $this->shippingHelper = $shippingHelper;
        $this->checkoutSession = $checkoutSession;
    }

    public function getConfig()
    {
        if (!$this->helper->isOnCscheckoutPage()) {
            return [];
        }

        return [
            'isCscheckout' => true,
            'cssoft' => [
                'cscheckout' => [
                    'layout' => $this->helper->getCscheckoutLayout(),
                    'emailOnSeparateStep' => $this->helper->isEmailOnSeparateStep(),
                    'shipping' => [
                        'default_method' => $this->shippingHelper->getDefaultShippingMethod()
                    ],
                    'payment' => [
                        'default_method' => $this->paymentHelper->getDefaultPaymentMethod(),
                    ],
                    'dependencies' => [
                        'payment' => $this->getPaymentDependencies()
                    ]
                ]
            ]
        ];
    }

    protected function getPaymentDependencies()
    {
        $result = [];

        if (!$this->checkoutSession->getQuote()->getIsVirtual()) {
            $config = $this->scopeConfig->getValue(
                'payment',
                ScopeInterface::SCOPE_WEBSITE
            );
            foreach ($config as $code => $paymentConfig) {
                if (!empty($paymentConfig['active']) &&
                    !empty($paymentConfig['allowspecific'])
                ) {
                    $result[] = 'address';
                    break;
                }
            }
        }

        return $result;
    }
}
