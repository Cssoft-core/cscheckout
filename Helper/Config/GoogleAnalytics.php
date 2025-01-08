<?php

namespace CSSoft\Cscheckout\Helper\Config;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class GoogleAnalytics extends AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_ENABLED = 'cscheckout/analytics/google_analytics_enabled';

    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    private $helper;

    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \CSSoft\Cscheckout\Helper\Data $helper
    ) {
        parent::__construct($context);

        $this->helper = $helper;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool) $this->helper->getConfigValue(self::CONFIG_PATH_ENABLED);
    }

    /**
     * @return boolean
     */
    public function isGTagEnabled()
    {
        return (bool) $this->helper->getConfigValue('google/gtag/analytics4/active');
    }

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return !$this->isEnabled();
    }
}
