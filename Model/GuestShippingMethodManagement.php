<?php

namespace CSSoft\Cscheckout\Model;

class GuestShippingMethodManagement
    implements \CSSoft\Cscheckout\Api\GuestShippingMethodManagementInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \CSSoft\Cscheckout\Api\ShippingMethodManagementInterface
     */
    protected $shippingMethodManagement;

    /**
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \CSSoft\Cscheckout\Api\ShippingMethodManagementInterface $shippingMethodManagement
     */
    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \CSSoft\Cscheckout\Api\ShippingMethodManagementInterface $shippingMethodManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->shippingMethodManagement = $shippingMethodManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function saveShippingMethod(
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->shippingMethodManagement->saveShippingMethod(
            $quoteIdMask->getQuoteId(),
            $addressInformation
        );
    }
}
