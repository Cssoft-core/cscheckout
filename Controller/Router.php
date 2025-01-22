<?php

namespace CSSoft\Cscheckout\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Page view helper
     *
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \CSSoft\Cscheckout\Helper\Data $pageViewHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \CSSoft\Cscheckout\Helper\Data $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    /**
     * Match cscheckout page
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->helper->isCscheckoutEnabled() || $request->getParam('onepage')) {
            return null;
        }

        $currentPath = trim($request->getPathInfo(), '/');
        if (strpos($currentPath, 'cscheckout') === 0) {
            return null; // use standard router to prevent recursion
        }

        $cscheckoutPath = $this->helper->getCscheckoutUrlPath();
        $cscheckoutPaths = [
            $cscheckoutPath,
            $cscheckoutPath . '/index',
            $cscheckoutPath . '/index/index',
        ];

        if (!in_array($currentPath, $cscheckoutPaths)) {
            return null;
        }

        $request->setAlias(
            \Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $currentPath
        );
        $request->setPathInfo('/cscheckout/index/index');

        return $this->actionFactory->create(
            \Magento\Framework\App\Action\Forward::class
        );
    }
}
