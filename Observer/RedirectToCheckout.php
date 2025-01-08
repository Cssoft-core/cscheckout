<?php

namespace CSSoft\Cscheckout\Observer;

class RedirectToCheckout implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    protected $helper;

    /**
     */
    public function __construct(
        \CSSoft\Cscheckout\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Set redirect to cscheckout after add to cart
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // if this solution works bad then implement plugin for
        // \Magento\Checkout\Controller\Cart::getBackUrl
        if ($this->helper->isCscheckoutEnabled()
            && $this->helper->isRedirectToCscheckoutEnabled()
        ) {
            $request = $observer->getRequest();
            if (!$request->getParam('return_url')) {
                $request->setParam(
                    'return_url',
                    $this->helper->getCscheckoutUrl()
                );
            }
        }
    }
}
