<?php

namespace CSSoft\Cscheckout\Helper\Config;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Shipping extends AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_DEFAULT_SHIPPING_METHOD = 'cscheckout/shipping/default_method';

    /**
     * @var string
     */
    const CONFIG_PATH_DEFAULT_SHIPPING_METHOD_CODE = 'cscheckout/shipping/default_method_code';

    /**
     * @var string
     */
    const CONFIG_PATH_HIDE_SHIPPING_METHODS = 'cscheckout/shipping/hide_methods';

    /**
     * @var string
     */
    const CONFIG_PATH_SORT_SHIPPING_METHODS_BY_PRICE = 'cscheckout/shipping/sort_by_price';

    /**
     * @var \CSSoft\Cscheckout\Helper\Data $cscheckoutHelper
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
    public function getDefaultShippingMethod()
    {
        $method = $this->cscheckoutHelper->getConfigValue(
            self::CONFIG_PATH_DEFAULT_SHIPPING_METHOD_CODE,
            ScopeInterface::SCOPE_WEBSITE
        );

        if (!$method) {
            $method = $this->cscheckoutHelper->getConfigValue(
                self::CONFIG_PATH_DEFAULT_SHIPPING_METHOD,
                ScopeInterface::SCOPE_WEBSITE
            );
        }

        return $method;
    }

    /**
     * @return boolean
     */
    public function getHideShippingMethods()
    {
        return (bool) $this->cscheckoutHelper->getConfigValue(
            self::CONFIG_PATH_HIDE_SHIPPING_METHODS,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return boolean
     */
    public function getSortShippingMethodsByPrice()
    {
        return (bool) $this->cscheckoutHelper->getConfigValue(
            self::CONFIG_PATH_SORT_SHIPPING_METHODS_BY_PRICE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
