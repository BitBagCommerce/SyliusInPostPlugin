@managing_shipping_export_inpost
Feature: Managing shipping gateway
    In order to export shipping data to external shipping provider service
    As an Administrator
    I want to be able to export shipments to external API

    Background:
        Given the store operates on a single channel in the "United States" named "Web-US"
        And I am logged in as an administrator
        And the store has "Inpost" shipping method with "$10.00" fee
        And there is a registered "inpost" shipping gateway for this shipping method named "Inpost"
        And it has "Access token" field set to "123"
        And it has "Organization ID" field set to "123"
        And it has "Environment" field set to "sandbox"
        And it has "Service" field set to "inpost_courier_standard"
        And the store has a product "Chicken" priced at "$2.00" in "Web-US" channel
        And customer "user@bitbag.pl" has placed 5 orders on the "Web-US" channel in each buying 5 "Chicken" products
        And the customer set the shipping address "Mike Ross" addressed it to "350 5th Ave", "10118" "New York" in the "United States" to orders
        And those orders were placed with "Inpost" shipping method
        And set product weight to "10"
        And set units to the shipment

    @ui
    Scenario: Seeing shipments to export
        When I go to the shipping export page
        Then I should see 5 shipments with "New" state
