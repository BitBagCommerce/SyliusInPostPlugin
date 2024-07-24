<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\BitBag\SyliusInPostPlugin\Behat\Page\Admin\ShippingGateway\CreatePageInterface;
use Webmozart\Assert\Assert;

final class ShippingGatewayContext implements Context
{
    /** @var CreatePageInterface */
    private $createPage;

    private CurrentPageResolverInterface $currentPageResolver;

    private NotificationCheckerInterface $notificationChecker;

    public function __construct(
        CreatePageInterface $createPage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker,
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
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();
        $currentPage->selectShippingMethod($name);
    }

    /**
     * @When I fill the :field field with :value
     */
    public function iFillTheFieldWith(string $field, string $value): void
    {
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();
        $currentPage->fillField($field, $value);
    }

    /**
     * @When I clear the :field field
     */
    public function iClearTheField(string $field): void
    {
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();
        $currentPage->fillField($field, '');
    }

    /**
     * @When I add it
     * @When I save it
     */
    public function iTryToAddIt(): void
    {
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();
        $currentPage->submit();
    }

    /**
     * @Then I should be notified that the shipping gateway has been created
     * @Then I should be notified that the shipping gateway has been updated
     */
    public function iShouldBeNotifiedThatTheShippingGatewayWasCreated(): void
    {
        $this->notificationChecker->checkNotification(
            'Shipping gateway has been successfully',
            NotificationType::success(),
        );
    }

    /**
     * @Then :message error message should be displayed
     */
    public function errorMessageForFieldShouldBeDisplayed(string $message): void
    {
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();
        Assert::true($currentPage->hasError($message));
    }

    /**
     * @When I fill the :field select option with :option
     */
    public function iFillTheSelectOptionWith(string $filed, string $option): void
    {
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();
        $currentPage->selectFieldOption($filed, $option);
    }

    /**
     * @return CreatePageInterface|SymfonyPageInterface
     */
    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
        ]);
    }
}
