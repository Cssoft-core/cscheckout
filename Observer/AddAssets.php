<?php

namespace CSSoft\Cscheckout\Observer;

class AddAssets implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    private $helper;

    /**
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \CSSoft\Cscheckout\Helper\Data   $helper
     */
    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \CSSoft\Cscheckout\Helper\Data $helper
    ) {
        $this->pageConfig = $pageConfig;
        $this->helper = $helper;
    }

    /**
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isOnCscheckoutPage()) {
            return;
        }

        $this->pageConfig->addPageAsset(
            'CSSoft_Cscheckout::css/cscheckout-' . $this->helper->getTheme() . '.css',
            [
                'attributes' => [
                    'media' => 'screen, print',
                ]
            ]
        );
    }
}
