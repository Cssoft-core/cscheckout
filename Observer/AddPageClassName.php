<?php

namespace CSSoft\Cscheckout\Observer;

class AddPageClassName implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    protected $helper;

    /**
     * @var \CSSoft\Cscheckout\Helper\Config\Shipping
     */
    protected $shippingHelper;

    /**
     */
    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \CSSoft\Cscheckout\Helper\Data $helper,
        \CSSoft\Cscheckout\Helper\Config\Shipping $shippingHelper
    ) {
        $this->pageConfig = $pageConfig;
        $this->helper = $helper;
        $this->shippingHelper = $shippingHelper;
    }

    /**
     * Add FontAwesome assets according to module config
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isOnCscheckoutPage()) {
            return;
        }

        $this->pageConfig->addBodyClass('cscheckout')
            ->addBodyClass('checkout-index-index')
            ->addBodyClass('fc-form-' . $this->helper->getFormStyle())
            ->addBodyClass('fc-theme-' . $this->helper->getTheme());

        if ($this->helper->getHideLabels()) {
            $this->pageConfig->addBodyClass('fc-form-hide-labels');
        }

        if (!$this->helper->getDisableTooltips()) {
            $this->pageConfig->addBodyClass('fc-form-tooltips');
        }

        if ($this->shippingHelper->getHideShippingMethods()) {
            $this->pageConfig->addBodyClass('fc-hide-shipping-methods');
        }

        foreach ($this->helper->getLayoutClassNames() as $class) {
            $this->pageConfig->addBodyClass($class);
        }
    }
}
