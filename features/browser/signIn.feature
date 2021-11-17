@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
  Feature: As an user I can sign in using form

  @APIUser:admin
  Scenario: User is asked for setting a new password when his password is in an unsupported format
    Given I create a user "UnsupportedPasswordUser" with last name "User" in group "Anonymous Users"
    And a user "UnsupportedPasswordUser" has password in unsupported format
    When I am viewing the pages on siteaccess "site" as "UnsupportedPasswordUser"
    Then the url should match "/site/user/forgot-password/migration"
    And I should see "Your password has expired"

  @APIUser:admin
  Scenario: Create test admin user with known email
    Given I create a user "testadmin" with last name "User" and known email in group "Administrator users"

  Scenario: User can log in on frontend using email
    Given I am viewing the pages on siteaccess "site" as "testadmin@example.com"
    When I go to "users"
    Then the url should match "/Users"
    And the response status code should be 200

  @javascript @lorem
  Scenario: User can log in to backoffice using email
    Given I open Login page in admin SiteAccess
    When I log in as "testadmin@example.com"
    Then the url should match "/admin/dashboard"
    And I should be on Dashboard page
