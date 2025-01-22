<?php

namespace CSSoft\Cscheckout\Plugin\Model;

class QuoteManagement
{
    /**
     * @var \CSSoft\Cscheckout\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param \CSSoft\Cscheckout\Helper\Data $helper
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \CSSoft\Cscheckout\Helper\Data $helper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function beforePlaceOrder(
        \Magento\Quote\Api\CartManagementInterface $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod = null
    ) {
        if (!$this->helper->isCscheckoutEnabled()) {
            return;
        }

        try {
            $quote = $this->quoteRepository->getActive($cartId);
        } catch (\Exception $e) {
            return;
        }

        if ($quote->getCustomerEmail()) {
            return;
        }

        $email = $quote->getBillingAddress()->getEmail();

        if (!$email) {
            $email = $quote->getShippingAddress()->getEmail();
        }

        if (!$email && $quote->getCustomerId()) {
            $email = $quote->getCustomer()->getEmail();
        }

        // Fix for invalidly imported quotes without email address
        if ($email) {
            $quote->setCustomerEmail($email);
        }
    }
}
