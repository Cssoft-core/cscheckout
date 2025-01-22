<?php

namespace CSSoft\Cscheckout\Plugin\Block;

class OnepageLink
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
     * @param \Magento\Checkout\Block\Onepage\Link $subject
     * @param string $result
     * @return string
     */
    public function afterGetCheckoutUrl(
        \Magento\Checkout\Block\Onepage\Link $subject,
        $result
    ) {
        if ($this->helper->isCscheckoutEnabled()) {
            return $this->helper->getCscheckoutUrl();
        }
        return $result;
    }

    /**
     * @param \Magento\Checkout\Block\Onepage\Link $subject
     * @param boolean $result
     * @return boolean
     */
    public function afterIsPossibleOnepageCheckout(
        \Magento\Checkout\Block\Onepage\Link $subject,
        $result
    ) {
        if ($this->helper->isCscheckoutEnabled()) {
            return true;
        }
        return $result;
    }
}
