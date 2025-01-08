<?php

namespace CSSoft\Cscheckout\Plugin\Model;

class ShippingMethodManagement
{
    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    private $helper;

    /**
     * @var \CSSoft\Cscheckout\Helper\Config\Shipping
     */
    private $shippingHelper;

    /**
     * @param \CSSoft\Cscheckout\Helper\Data $helper
     * @param \CSSoft\Cscheckout\Helper\Config\Shipping $shippingHelper
     */
    public function __construct(
        \CSSoft\Cscheckout\Helper\Data $helper,
        \CSSoft\Cscheckout\Helper\Config\Shipping $shippingHelper
    ) {
        $this->helper = $helper;
        $this->shippingHelper = $shippingHelper;
    }

    /**
     * @param \Magento\Quote\Api\ShippingMethodManagementInterface $subject
     * @param array $rates
     * @return array
     */
    public function afterEstimateByAddress(
        \Magento\Quote\Api\ShippingMethodManagementInterface $subject,
        $rates
    ) {
        return $this->sort($rates);
    }

    /**
     * @param \Magento\Quote\Api\ShippingMethodManagementInterface $subject
     * @param array $rates
     * @return array
     */
    public function afterEstimateByExtendedAddress(
        \Magento\Quote\Api\ShippingMethodManagementInterface $subject,
        $rates
    ) {
        return $this->sort($rates);
    }

    /**
     * @param \Magento\Quote\Api\ShippingMethodManagementInterface $subject
     * @param array $rates
     * @return array
     */
    public function afterEstimateByAddressId(
        \Magento\Quote\Api\ShippingMethodManagementInterface $subject,
        $rates
    ) {
        return $this->sort($rates);
    }

    /**
     * @param array $rates
     * @return array
     */
    private function sort($rates)
    {
        if (!$this->helper->isCscheckoutEnabled() ||
            !$this->shippingHelper->getSortShippingMethodsByPrice()
        ) {
            return $rates;
        }

        usort($rates, function ($a, $b) {
            return $a->getAmount() <=> $b->getAmount();
        });

        return $rates;
    }
}
