<?php

namespace CSSoft\Cscheckout\Helper\Config;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class JsBuild extends AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_ENABLED = 'cscheckout/performance/jsbuild';

    /**
     * @var string
     */
    const CONFIG_PATH_PREFETCH_ENABLED = 'cscheckout/performance/prefetch';

    /**
     * @var string
     */
    const CONFIG_PATH_PATHS = 'cscheckout/performance/jsbuild_paths';

    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    private $helper;

    /**
     * @param Context $context
     * \CSSoft\Cscheckout\Helper\Data $helper
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
    public function isPrefetchEnabled()
    {
        return (bool) $this->helper->getConfigValue(self::CONFIG_PATH_PREFETCH_ENABLED);
    }

    /**
     * @return array
     */
    public function getIncludePaths()
    {
        $paths = explode("\n", $this->helper->getConfigValue(self::CONFIG_PATH_PATHS));
        return array_filter($paths);
    }
}
