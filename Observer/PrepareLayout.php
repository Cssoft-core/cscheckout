<?php

namespace CSSoft\Cscheckout\Observer;

class PrepareLayout implements \Magento\Framework\Event\ObserverInterface
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

        switch ($this->helper->getPageLayout()) {
            case 'full':
                $handle = 'cscheckout_layout_full';
                break;
            case 'minimal':
                $handle = 'cscheckout_layout_minimal';
                break;
            case 'empty':
                $handle = 'cscheckout_layout_empty';
                break;
            case 'default';
                return;
            default:
                return;
        }

        $observer->getLayout()->getUpdate()->addHandle($handle);
    }
}
