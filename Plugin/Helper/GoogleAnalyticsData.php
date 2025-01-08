<?php

namespace CSSoft\Cscheckout\Plugin\Helper;

class GoogleAnalyticsData
{
    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    private $helper;

    /**
     * @var \CSSoft\Cscheckout\Helper\Config\GoogleAnalytics
     */
    private $googleAnalyticsHelper;

    /**
     * @param \CSSoft\Cscheckout\Helper\Data $helper
     * @param \CSSoft\Cscheckout\Helper\Config\GoogleAnalytics $googleAnalyticsHelper
     */
    public function __construct(
        \CSSoft\Cscheckout\Helper\Data $helper,
        \CSSoft\Cscheckout\Helper\Config\GoogleAnalytics $googleAnalyticsHelper
    ) {
        $this->helper = $helper;
        $this->googleAnalyticsHelper = $googleAnalyticsHelper;
    }

    /**
     * Enable Magento's GA integration on checkout and checkout success pages if
     * it's enabled in cscheckout config.
     *
     * @param \Magento\GoogleAnalytics\Helper\Data $subject
     * @param boolean $result
     * @return boolean
     */
    public function afterIsGoogleAnalyticsAvailable(
        \Magento\GoogleAnalytics\Helper\Data $subject,
        $result
    ) {
        if ($result || $this->googleAnalyticsHelper->isGTagEnabled()) {
            return $result;
        }

        if (!$this->googleAnalyticsHelper->isEnabled()) {
            return false;
        }

        if ($this->helper->isOnCscheckoutPage()) {
            return true;
        }

        return $this->helper->isCscheckoutEnabled()
            && $this->helper->isOnSuccessPage();
    }
}
