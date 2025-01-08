<?php

namespace CSSoft\Cscheckout\Block;

class CustomCssJs extends \Magento\Framework\View\Element\Template
{
    const CONFIG_PATH_CSS = 'cscheckout/custom_css_js/css';

    const CONFIG_PATH_JS = 'cscheckout/custom_css_js/js';

    /**
     * @var string
     */
    protected $_template = 'CSSoft_Cscheckout::custom_css_js.phtml';

    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    private $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \CSSoft\Cscheckout\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \CSSoft\Cscheckout\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return string
     */
    public function getCss()
    {
        return $this->helper->getConfigValue(self::CONFIG_PATH_CSS);
    }

    /**
     * @return string
     */
    public function getJs()
    {
        return str_replace('define([', 'require([', (string)$this->helper->getConfigValue(self::CONFIG_PATH_JS));
    }
}
