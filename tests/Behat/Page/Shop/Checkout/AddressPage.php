<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\Shop\Checkout\AddressPage as BaseAddressPage;
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Service\JQueryHelper;
use Sylius\Component\Core\Model\AddressInterface;
use Webmozart\Assert\Assert;

class AddressPage extends BaseAddressPage implements AddressPageInterface
{
    public function specifyShippingAddress(AddressInterface $shippingAddress): void
    {
        $this->specifyAddress($shippingAddress, self::TYPE_SHIPPING);
    }

    public function specifyBillingAddress(AddressInterface $billingAddress): void
    {
        $this->specifyAddress($billingAddress, self::TYPE_BILLING);
    }
    
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_phone_number' => '[name="sylius_checkout_address[billingAddress][phoneNumber]"]',
            'shipping_phone_number' => '[name="sylius_checkout_address[shippingAddress][phoneNumber]"]',
        ]);
    }

    private function specifyAddress(AddressInterface $address, string $type): void
    {
        $this->assertAddressType($type);

        $this->getElement(sprintf('%s_first_name', $type))->setValue($address->getFirstName());
        $this->getElement(sprintf('%s_last_name', $type))->setValue($address->getLastName());
        $this->getElement(sprintf('%s_street', $type))->setValue($address->getStreet());
        $this->getElement(sprintf('%s_country', $type))->selectOption($address->getCountryCode() ?: 'Select');
        $this->getElement(sprintf('%s_city', $type))->setValue($address->getCity());
        $this->getElement(sprintf('%s_postcode', $type))->setValue($address->getPostcode());

        JQueryHelper::waitForFormToStopLoading($this->getDocument());

        if (null !== $address->getPhoneNumber()) {
            $this->getElement(sprintf('%s_phone_number', $type))->setValue($address->getPhoneNumber());
        }

        if (null !== $address->getProvinceName()) {
            $this->waitForElement(5, sprintf('%s_province', $type));
            $this->getElement(sprintf('%s_province', $type))->setValue($address->getProvinceName());
        }
        if (null !== $address->getProvinceCode()) {
            $this->waitForElement(5, sprintf('%s_country_province', $type));
            $this->getElement(sprintf('%s_country_province', $type))->selectOption($address->getProvinceCode());
        }
    }

    private function waitForElement(int $timeout, string $elementName): bool
    {
        return $this->getDocument()->waitFor($timeout, fn () => $this->hasElement($elementName));
    }

    private function assertAddressType(string $type): void
    {
        $availableTypes = [self::TYPE_BILLING, self::TYPE_SHIPPING];

        Assert::oneOf($type, $availableTypes, sprintf('There are only two available types %s, %s. %s given', self::TYPE_BILLING, self::TYPE_SHIPPING, $type));
    }
}
