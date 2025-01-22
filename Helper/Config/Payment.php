<?php

namespace CSSoft\Cscheckout\Helper\Config;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use CSSoft\Cscheckout\Model\Config\Source\BillingAddressDisplayOptions;
use CSSoft\Cscheckout\Model\Config\Source\BillingAddressSaveModes;

class Payment extends AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_DEFAULT_PAYMENT_METHOD = 'cscheckout/payment/default_method';

    /**
     * @var string
     */
    const CONFIG_PATH_DISPLAY_BILLING_ADDRESS_TITLE = 'cscheckout/payment/display_billing_address_title';

    /**
     * @var string
     */
    const CONFIG_PATH_DISPLAY_BILLING_ADDRESS_ON = 'cscheckout/payment/display_billing_address_on';

    /**
     * @var string
     */
    const CONFIG_PATH_BILLING_ADDRESS_SAVE_MODE = 'cscheckout/payment/billing_address_save_mode';

    /**
     * @var \CSSoft\Cscheckout\Helper\Data $cscheckoutHelper
     */
    private $cscheckoutHelper;

    /**
     * @param Context $context
     * \CSSoft\Cscheckout\Helper\Data $cscheckoutHelper
     */
    public function __construct(
        Context $context,
        \CSSoft\Cscheckout\Helper\Data $cscheckoutHelper
    ) {
        parent::__construct($context);

        $this->cscheckoutHelper = $cscheckoutHelper;
    }

    /**
     * @return string
     */
    public function getDefaultPaymentMethod()
    {
        return $this->cscheckoutHelper->getConfigValue(
            self::CONFIG_PATH_DEFAULT_PAYMENT_METHOD,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getDisplayBillingAddressOn()
    {
        $value = $this->cscheckoutHelper->getConfigValue(
            self::CONFIG_PATH_DISPLAY_BILLING_ADDRESS_ON
        );

        if (!$value || $value === BillingAddressDisplayOptions::OPTION_MAGENTO_CONFIG) {
            $this->cscheckoutHelper->getConfigValue(
                'checkout/options/display_billing_address_on'
            );
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getBillingAddressTitle()
    {
        return $this->cscheckoutHelper->getConfigValue(
            self::CONFIG_PATH_DISPLAY_BILLING_ADDRESS_TITLE
        ) ? __('Billing Address') : '';
    }

    /**
     * @return array
     */
    public function getBillingAddressJsConfig()
    {
        return [
            'title' => $this->getBillingAddressTitle(),
            'position' => $this->getDisplayBillingAddressOn(),
        ];
    }

    /**
     * @return boolean
     */
    public function isBillingAddressInstantSaveDisabled()
    {
        return $this->cscheckoutHelper->getConfigValue(
            self::CONFIG_PATH_BILLING_ADDRESS_SAVE_MODE
        ) !== BillingAddressSaveModes::MODE_INSTANT;
    }
}
