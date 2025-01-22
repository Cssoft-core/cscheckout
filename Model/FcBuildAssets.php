<?php

namespace CSSoft\Cscheckout\Model;

use CSSoft\Cscheckout\Helper\Config\JsBuild as JsBuildHelper;
use CSSoft\Cscheckout\Model\RequireJs\JsBuildFactory;

class FcBuildAssets
{
    private JsBuildHelper $jsBuildHelper;

    private JsBuildFactory $jsBuildFactory;

    public function __construct(
        JsBuildHelper $jsBuildHelper,
        JsBuildFactory $jsBuildFactory
    ) {
        $this->jsBuildHelper = $jsBuildHelper;
        $this->jsBuildFactory = $jsBuildFactory;
    }

    public function createAssets(): array
    {
        if (!$includePaths = $this->jsBuildHelper->getIncludePaths()) {
            return [];
        }

        $build = $this->jsBuildFactory->create([
            'name' => 'all',
            'paths' => $includePaths,
        ]);

        return [
            $build->publishIfNotExist()->getAsset(),
            $build->createStaticJsAsset(),
        ];
    }
}
