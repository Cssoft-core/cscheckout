<?php

namespace CSSoft\Cscheckout\Helper\Config;

use Magento\CheckoutAgreements\Model\AgreementsProvider;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use CSSoft\Cscheckout\Model\Config\Source\AgreementsPosition;

class Agreements extends AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_TITLE = 'cscheckout/agreements/title';

    /**
     * @var string
     */
    const CONFIG_PATH_POSITION = 'cscheckout/agreements/position';

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
    public function getPosition()
    {
        return $this->cscheckoutHelper->getConfigValue(self::CONFIG_PATH_POSITION);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->cscheckoutHelper->getConfigValue(self::CONFIG_PATH_TITLE);
    }

    /**
     * @return boolean
     */
    public function isMoverDisabled()
    {
        if (!$this->cscheckoutHelper->getConfigValue(AgreementsProvider::PATH_ENABLED)) {
            return true;
        }

        return $this->getPosition() === AgreementsPosition::VALUE_EMTPY;
    }

    /**
     * @return array
     */
    public function getMoverJsConfig()
    {
        return [
            'title' => $this->getTitle(),
            'position' => $this->getPosition(),
        ];
    }
}
