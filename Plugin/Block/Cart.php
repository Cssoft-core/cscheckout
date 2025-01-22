<?php

namespace CSSoft\Cscheckout\Plugin\Block;

class Cart
{
    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    protected $helper;

    /**
     * @param \CSSoft\Cscheckout\Helper\Data $helper
     */
    public function __construct(
        \CSSoft\Cscheckout\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Checkout\Block\Cart $subject
     * @param string $result
     * @return string
     */
    public function afterGetCheckoutUrl(
        \Magento\Checkout\Block\Cart $subject,
        $result
    ) {
        if ($this->helper->isCscheckoutEnabled()) {
            return $this->helper->getCscheckoutUrl();
        }
        return $result;
    }
}
