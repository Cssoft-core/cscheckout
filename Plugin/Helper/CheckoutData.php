<?php

namespace CSSoft\Cscheckout\Plugin\Helper;

class CheckoutData
{
    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    protected $helper;

    /**
     * @var \CSSoft\Cscheckout\Helper\Config\Payment
     */
    protected $paymentConfig;

    /**
     * @param \CSSoft\Cscheckout\Helper\Data $helper
     * @param \CSSoft\Cscheckout\Helper\Config\Payment $paymentConfig
     */
    public function __construct(
        \CSSoft\Cscheckout\Helper\Data $helper,
        \CSSoft\Cscheckout\Helper\Config\Payment $paymentConfig
    ) {
        $this->helper = $helper;
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * Overriden to return true, when onepage checkout is disabled.
     *
     * This check is used to render header cart actions and subtotal
     *
     * @param \Magento\Checkout\Helper\Data $subject
     * @param boolean $result
     * @return boolean
     */
    public function afterCanOnepageCheckout(
        \Magento\Checkout\Helper\Data $subject,
        $result
    ) {
        if ($this->helper->isCscheckoutEnabled()) {
            return true;
        }
        return $result;
    }

    /**
     * Programmatically move address to payment page if cscheckout config
     * wants to show billing address in custom place.
     *
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterIsDisplayBillingOnPaymentMethodAvailable(
        $subject,
        $result
    ) {
        if (!$this->helper->isOnCscheckoutPage()) {
            // address is already on the payment page
            return $result;
        }

        $value = $this->paymentConfig->getDisplayBillingAddressOn();
        $value = str_replace('default-', '', $value); // cscheckout supports default values as well

        return (bool) !$value;
    }
}
