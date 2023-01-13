<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusInPostPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tests\BitBag\SyliusInPostPlugin\Behat\Page\Shop\Checkout\CompletePageInterface;
use Webmozart\Assert\Assert;

class PointContext implements Context
{
    public function __construct(
        private AbstractBrowser $client,
        private SessionInterface $session,
        private CompletePageInterface $completePage,
    ) {
    }

    /**
     * @When the ajax request to select point :name is sent
     */
    public function theAjaxRequestToSelectPointIsSent(string $name)
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request(
            'GET',
            '/point',
            ['name' => $name],
            [],
            ['ACCEPT' => 'application/json'],
        );
    }

    /**
     * @Then I should see the selected point is :name
     */
    public function iShouldSeeTheSelectedPoint(string $name)
    {
        Assert::true($this->completePage->hasPointName($name));
    }
}
