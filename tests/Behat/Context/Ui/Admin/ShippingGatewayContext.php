<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\BitBag\SyliusInPostPlugin\Behat\Page\Admin\ShippingGateway\CreatePageInterface;
use Tests\BitBag\SyliusShippingExportPlugin\Behat\Behaviour\ContainsError;
use Webmozart\Assert\Assert;

final class ShippingGatewayContext implements Context
{
    /** @var CreatePageInterface|ContainsError */
    private $createPage;

    private CurrentPageResolverInterface $currentPageResolver;

    private NotificationCheckerInterface $notificationChecker;

    public function __construct(
        CreatePageInterface $createPage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I visit the create shipping gateway configuration page for :code
     */
    public function iVisitTheCreateShippingGatewayConfigurationPage(string $code): void
    {
        $this->createPage->open(['code' => $code]);
    }

    /**
     * @When I select the :name shipping method
     */
    public function iSelectTheShippingMethod(string $name): void
    {
        $this->resolveCurrentPage()->selectShippingMethod($name);
    }

    /**
     * @When I fill the :field field with :value
     */
    public function iFillTheFieldWith(string $field, string $value): void
    {
        $this->resolveCurrentPage()->fillField($field, $value);
    }

    /**
     * @When I clear the :field field
     */
    public function iClearTheField(string $field): void
    {
        $this->resolveCurrentPage()->fillField($field, '');
    }

    /**
     * @When I add it
     * @When I save it
     */
    public function iTryToAddIt(): void
    {
        $this->resolveCurrentPage()->submit();
    }

    /**
     * @Then I should be notified that the shipping gateway has been created
     * @Then I should be notified that the shipping gateway has been updated
     */
    public function iShouldBeNotifiedThatTheShippingGatewayWasCreated(): void
    {
        $this->notificationChecker->checkNotification(
            'Shipping gateway has been successfully',
            NotificationType::success()
        );
    }

    /**
     * @Then :message error message should be displayed
     */
    public function errorMessageForFieldShouldBeDisplayed(string $message): void
    {
        Assert::true($this->resolveCurrentPage()->hasError($message));
    }

    /**
     * @When I fill the :field select option with :option
     */
    public function iFillTheSelectOptionWith(string $filed, string $option): void
    {
        $this->resolveCurrentPage()->selectFieldOption($filed, $option);
    }

    /**
     * @return CreatePageInterface|ContainsError|SymfonyPageInterface
     */
    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
        ]);
    }
}
