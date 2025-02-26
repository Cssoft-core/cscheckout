<?php

namespace CSSoft\Cscheckout\Model;

class ShippingMethodManagement
    extends ShippingAddressManagement
    implements \CSSoft\Cscheckout\Api\ShippingMethodManagementInterface
{
    /**
     * saveAddressInformation is not used directly to disable all third-party
     * module validators and plugins at this point
     */
    public function saveShippingMethod(
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        return parent::saveShippingAddress($cartId, $addressInformation);
    }
}
