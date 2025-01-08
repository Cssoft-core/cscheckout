<?php

namespace CSSoft\Cscheckout\Helper\Config;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class OrderSummary extends AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_SHOW_ORDER_REVIEW = 'cscheckout/order_summary/show_order_review';

    /**
     * @var string
     */
    const CONFIG_PATH_TITLE = 'cscheckout/order_summary/title';

    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    private $cscheckoutHelper;

    /**
     * @param Context $context
     * \CSSoft\Cscheckout\Helper\Data $cscheckoutHelper
     */
    public function __construct(
        Context $context,
        \CSSoft\Cscheckout\Helper\Data $cscheckoutHelper
    ) {
        parent::__construct($context);

        $this->cscheckoutHelper = $cscheckoutHelper;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->cscheckoutHelper->getConfigValue(self::CONFIG_PATH_TITLE);
    }

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return !$this->cscheckoutHelper->getConfigValue(self::CONFIG_PATH_SHOW_ORDER_REVIEW);
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        return [
            'title' => $this->getTitle(),
        ];
    }
}
