<?php

namespace CSSoft\Cscheckout\Plugin\View;

class AssetSource
{
    const FILENAME = '_theme-editor.less';

    const PLACEHOLDER = '// @fc-theme-editor';

    const CONFIG_PATH_CSS = 'cscheckout/custom_css_js/less';

    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \CSSoft\Cscheckout\Helper\Data $helper
     */
    public function __construct(
        \CSSoft\Cscheckout\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    /**
     * After is not used for Magento 2.2 compatibility.
     * (Params are not passed in "after" methods)
     *
     * @param mixed $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetContent(
        \Magento\Framework\View\Asset\Source $subject,
        callable $proceed,
        \Magento\Framework\View\Asset\LocalInterface $asset
    ) {
        $result = $proceed($asset);

        $filepath = $asset->getSourceFile();
        $filename = basename($filepath);

        if ($result &&
            strpos($filename, self::FILENAME) !== false &&
            strpos($result, self::PLACEHOLDER) !== false
        ) {
            $result = str_replace(
                self::PLACEHOLDER,
                (string) $this->helper->getConfigValue(self::CONFIG_PATH_CSS),
                $result
            );
        }

        return $result;
    }
}
