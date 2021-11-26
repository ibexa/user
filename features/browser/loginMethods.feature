@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: As an user I can log in using email address

  @APIUser:admin
  Scenario: Create test admin user with known email
    Given I create a user "testadmin" with last name "User" in group "Administrator users" with email "testadmin@example.com"

  Scenario: User can log in on frontend using email
    Given I am viewing the pages on siteaccess "site" as "testadmin@example.com"
    When I go to "users"
    Then the url should match "/Users"
    And the response status code should be 200

  @javascript
  Scenario: User can log in to backoffice using email
    Given I open Login page in admin SiteAccess
    When I log in as "testadmin@example.com"
    Then the url should match "/admin/dashboard"
    And I should be on Dashboard page
