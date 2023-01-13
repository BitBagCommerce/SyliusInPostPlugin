@adding_point_to_cart
Feature: Adding point to cart
    In order to ship my order properly
    As a Customer
    I want to be able to add point to my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Targaryen T-Shirt"
        And the store allows shipping with "Inpost point" identified by "inpost_point"
        And the store has a payment method "Online" with a code "Online"
        And I am a logged in customer

    @ui
    Scenario: Successfully adding point to cart
        Given I have product "Targaryen T-Shirt" in the cart
        And I specified the billing address
        When I select "Inpost point" shipping method
        And the ajax request to select point "KRA01A" is sent
        And I complete the addressing step
        And I complete the payment step
        Then I should be on the checkout summary step
        And I should see the selected point is "KRA01A"
