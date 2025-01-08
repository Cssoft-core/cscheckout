<?php

namespace CSSoft\Cscheckout\Block;

class Prefetch extends \Magento\Framework\View\Element\Text
{
    /**
     * @var \CSSoft\Cscheckout\Helper\Config
     */
    private $helper;

    /**
     * @var \CSSoft\Cscheckout\Helper\Config\JsBuild
     */
    private $jsBuild;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \CSSoft\Cscheckout\Helper\Data $helper
     * @param \CSSoft\Cscheckout\Helper\Config\JsBuild $jsBuild
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \CSSoft\Cscheckout\Helper\Data $helper,
        \CSSoft\Cscheckout\Helper\Config\JsBuild $jsBuild,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helper = $helper;
        $this->jsBuild = $jsBuild;
    }

    /**
     * @return string
     */
    public function getText()
    {
        if (!$this->helper->isCscheckoutEnabled() ||
            !$this->jsBuild->isEnabled() ||
            !$this->jsBuild->isPrefetchEnabled()
        ) {
            return '';
        }

        return $this->getData('text');
    }
}
