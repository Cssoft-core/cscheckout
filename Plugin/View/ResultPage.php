<?php

namespace CSSoft\Cscheckout\Plugin\View;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\LayoutInterface;
use CSSoft\Cscheckout\Helper\Config\JsBuild as JsBuildHelper;
use CSSoft\Cscheckout\Helper\Data as Helper;
use CSSoft\Cscheckout\Model\FcBuildAssets;

class ResultPage
{
    private LayoutInterface $layout;

    private Helper $helper;

    private JsBuildHelper $jsBuildHelper;

    private FcBuildAssets $fcBuildAssets;

    public function __construct(
        LayoutInterface $layout,
        Helper $helper,
        JsBuildHelper $jsBuildHelper,
        FcBuildAssets $fcBuildAssets
    ) {
        $this->layout = $layout;
        $this->helper = $helper;
        $this->jsBuildHelper = $jsBuildHelper;
        $this->fcBuildAssets = $fcBuildAssets;
    }

    public function afterRenderResult(
        ResultInterface $subject,
        ResultInterface $result,
        ResponseInterface $httpResponse
    ) {
        if (!$this->jsBuildHelper->isEnabled() || !$this->helper->isOnCscheckoutPage()) {
            return $result;
        }

        $html = $httpResponse->getBody();
        if (empty($html) || strpos($html, '<html') === false) {
            return $result;
        }

        $pos = $this->getInsertPosition($html);
        if ($pos) {
            $html = substr_replace($html, "\n" . $this->renderFcBuild(), $pos, 0);
            $httpResponse->setBody($html);
        }

        return $result;
    }

    private function getInsertPosition($html)
    {
        $matches = [
            '/requirejs-min-resolver.min.js"></script>',
            '/requirejs.min.js"></script>',
            '/requirejs.js"></script>',
        ];

        foreach ($matches as $string) {
            $pos = strpos($html, $string);
            if ($pos !== false) {
                return $pos + strlen($string);
            }
        }

        preg_match('/\/merged\/[a-z0-9.]+"><\/script>/', $html, $matches, PREG_OFFSET_CAPTURE);

        if ($matches) {
            return $matches[0][1] + strlen($matches[0][0]);
        }
    }

    private function renderFcBuild()
    {
        $result = [];

        foreach ($this->fcBuildAssets->createAssets() as $asset) {
            $result[] = sprintf('<script src="%s"></script>', $asset->getUrl());
        }

        return implode("\n", $result);
    }
}
