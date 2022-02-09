@managing_shipping_gateway_inpost
Feature: Creating shipping gateway
    In order to export shipping data to external shipping provider service
    As an Administrator
    I want to be able to add new shipping gateway with shipping method

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And the store has "Inpost" shipping method with "$10.00" fee

    @ui
    Scenario: Creating Inpost shipping gateway
        When I visit the create shipping gateway configuration page for "inpost"
        And I select the "Inpost" shipping method
        And I fill the "Access token" field with "123"
        And I fill the "Organization ID" field with "123"
        And I fill the "Environment" field with "sandbox"
        And I fill the "Service" field with "inpost_courier_standard"
        And I add it
        Then I should be notified that the shipping gateway has been created
