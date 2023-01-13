@managing_shipping_image_inpost
Feature: Adding shipping method image
  In order to display shipping method image during shipment selection
  As an Administrator
  I want to be able to add image to shipping method

  Background:
    Given the store operates on a single channel in the "United States" named "Web-US"
    And I am logged in as an administrator
    And the store has "Inpost" shipping method with "$10.00" fee

  @ui
  Scenario: Seeing shipments to export
    When I want to modify a shipping method "Inpost"
    And I upload the "image/shipping_logo.jpg" image as shipping method logo
    And I save my changes
    Then I should be notified that it has been successfully edited