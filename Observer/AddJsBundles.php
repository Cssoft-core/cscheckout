<?php

namespace CSSoft\Cscheckout\Observer;

use Magento\Framework\App\State as AppState;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Asset\ConfigInterface as AssetConfig;
use CSSoft\Cscheckout\Helper\Config\JsBuild as JsBuildHelper;
use CSSoft\Cscheckout\Model\FcBuildAssets;

class AddJsBundles implements \Magento\Framework\Event\ObserverInterface
{
    private JsBuildHelper $jsBuildHelper;

    private FcBuildAssets $fcBuildAssets;

    private AssetConfig $assetConfig;

    private AppState $appState;

    public function __construct(
        JsBuildHelper $jsBuildHelper,
        FcBuildAssets $fcBuildAssets,
        AssetConfig $assetConfig,
        AppState $appState
    ) {
        $this->jsBuildHelper = $jsBuildHelper;
        $this->fcBuildAssets = $fcBuildAssets;
        $this->assetConfig = $assetConfig;
        $this->appState = $appState;
    }

    public function execute(Observer $observer)
    {
        if (!$this->jsBuildHelper->isEnabled()) {
            return;
        }

        if ($this->isProduction() && $this->assetConfig->isBundlingJsFiles()) {
            return;
        }

        $this->fcBuildAssets->createAssets();
    }

    private function isProduction()
    {
        return $this->appState->getMode() === AppState::MODE_PRODUCTION;
    }
}
