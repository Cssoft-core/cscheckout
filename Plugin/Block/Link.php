<?php

namespace CSSoft\Cscheckout\Plugin\Block;

class Link
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
     * @param \Magento\Checkout\Block\Link $subject
     * @param string $result
     * @return string
     */
    public function afterGetHref(
        \Magento\Checkout\Block\Link $subject,
        $result
    ) {
        if ($this->helper->isCscheckoutEnabled()) {
            return $this->helper->getCscheckoutUrl();
        }
        return $result;
    }
}
