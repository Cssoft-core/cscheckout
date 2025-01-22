<?php

namespace CSSoft\Cscheckout\Helper\Config;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use CSSoft\Cscheckout\Model\Config\Source\PlaceOrderPosition;

class PlaceOrder extends AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_POSITION = 'cscheckout/design/place_order_button_position';

    /**
     * @var \CSSoft\Cscheckout\Helper\Data $cscheckoutHelper
     */
    private $cscheckoutHelper;

    /**
     * @param Context $context
     * @param \CSSoft\Cscheckout\Helper\Data $cscheckoutHelper
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
    public function getPosition()
    {
        if (!$this->cscheckoutHelper->isOnecolumnLayout()) {
            return PlaceOrderPosition::VALUE_EMTPY;
        }

        return $this->cscheckoutHelper->getConfigValue(self::CONFIG_PATH_POSITION);
    }

    /**
     * @return boolean
     */
    public function isMoverDisabled()
    {
        return !$this->getPosition();
    }

    /**
     * @return array
     */
    public function getMoverJsConfig()
    {
        return [
            'position' => $this->getPosition(),
        ];
    }
}
