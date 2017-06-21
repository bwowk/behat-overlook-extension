Feature: Do not stop testing the outcomes of a scenario if one of the assertions fail

  As a software developer/tester
  I need scenarios to keep running after a failed "Then" step
  So I can validate all the expected outcomes of a scenario without having scenarios with redundant context and events

  Rules:
  - It is enabled on scenarios or outlines tagged @overlook.
  - It works for "Then" steps.
  - Steps following failed "Given" or "When" steps will still be skipped.
  - If the background fails, the scenario will still be skipped.
  - If a BeforeScenario hook fails, the scenario will still be skipped.

  Background:
    Given there is a file named "behat.yml" with:
    """
    default:
        extensions:
            bwowk\Behat\OverlookExtension: ~
    """
    And there is a file named "features/bootstrap/FeatureContext.php" with:
    """
    <?php
    use Behat\Behat\Context\Context;
    class FeatureContext implements Context
    {

      /**
       * @Given a passing step
       * @When a(nother) step succeeds
       * @Then a(nother) step should succeed
       */
       public function pass(){ echo "tested"; }

      /**
       * @Given a failing step
       * @When a(nother) step fails
       * @Then a(nother) step should fail
       * @BeforeScenario @hook_fails
       */
       public function fail(){ throw new Exception(); }

    }
    """

  Scenario: A scenario tagged @overlook should keep running "Then" steps after a "Then" step fails
    Given there is a file named "features/keep_running.feature" with:
      """
      Feature: Keep running

        @overlook
        Scenario: Keep Running steps
          Given a passing step
          When a step succeeds
          Then a step should fail
          But another step should succeed
      """
    When I run "behat features/keep_running.feature"
    Then it should fail with:
      """
          Given a passing step            # FeatureContext::pass()
            │ tested
          When a step succeeds            # FeatureContext::pass()
            │ tested
          Then a step should fail         # FeatureContext::fail()
            (Exception)
          But another step should succeed # FeatureContext::pass()
            │ tested
      """
    And the output should contain:
      """
      4 steps (3 passed, 1 failed)
      """

  Scenario: A scenario on a feature tagged @overlook should keep running "Then" steps after a "Then" step fails
    Given there is a file named "features/keep_running.feature" with:
      """
      @overlook
      Feature: Keep running
        
        Scenario: Keep Running steps
          Given a passing step
          When a step succeeds
          Then a step should fail
          But another step should succeed
      """
    When I run "behat features/keep_running.feature"
    Then it should fail with:
      """
          Given a passing step            # FeatureContext::pass()
            │ tested
          When a step succeeds            # FeatureContext::pass()
            │ tested
          Then a step should fail         # FeatureContext::fail()
            (Exception)
          But another step should succeed # FeatureContext::pass()
            │ tested
      """
    And the output should contain:
      """
      4 steps (3 passed, 1 failed)
      """

  Scenario: A scenario tagged @overlook should only keep running "Then" steps after a "Then" step fails
    Given there is a file named "features/keep_running.feature" with:
    """
    Feature: Keep running

      @overlook
      Scenario: Keep Running steps
        Given a passing step
        When a step succeeds
        Then a step should fail
        But another step should succeed
        When another step succeeds
        Then a step should succeed
    """
    When I run "behat features/keep_running.feature"
    Then it should fail with:
      """
          Given a passing step            # FeatureContext::pass()
            │ tested
          When a step succeeds            # FeatureContext::pass()
            │ tested
          Then a step should fail         # FeatureContext::fail()
            (Exception)
          But another step should succeed # FeatureContext::pass()
            │ tested
          When another step succeeds      # FeatureContext::pass()
          Then a step should succeed      # FeatureContext::pass()

      """
    And the output should contain:
      """
      6 steps (3 passed, 1 failed, 2 skipped)
      """

  Scenario: A scenario tagged @overlook should stop running after a "Given" step fails
    Given there is a file named "features/stop_running.feature" with:
    """
    Feature: Stop running

      @overlook
      Scenario: Keep Running steps
        Given a failing step
        When a step succeeds
        Then a step should fail
        Then another step should succeed
    """
    When I run "behat features/stop_running.feature"
    Then it should fail with:
      """
          Given a failing step             # FeatureContext::fail()
            (Exception)
          When a step succeeds             # FeatureContext::pass()
          Then a step should fail          # FeatureContext::fail()
          Then another step should succeed # FeatureContext::pass()

      """
    And the output should contain:
    """
    4 steps (1 failed, 3 skipped)
    """

  Scenario: A scenario tagged @overlook should stop running after a "When" step fails
    Given there is a file named "features/stop_running.feature" with:
    """
    Feature: Stop running

      @overlook
      Scenario: Keep Running steps
        Given a passing step
        When a step fails
        Then a step should fail
        And another step should succeed
    """
    When I run "behat features/stop_running.feature"
    Then it should fail with:
      """
          Given a passing step            # FeatureContext::pass()
            │ tested
          When a step fails               # FeatureContext::fail()
            (Exception)
          Then a step should fail         # FeatureContext::fail()
          And another step should succeed # FeatureContext::pass()

      """
    And the output should contain:
    """
    4 steps (1 passed, 1 failed, 2 skipped)
    """

  Scenario: A scenario tagged @overlook should be skipped if the background fails
    Given there is a file named "features/stop_running.feature" with:
    """
    Feature: Stop running

      Background:
        Given a failing step

      @overlook
      Scenario: Keep Running steps
        Given a passing step
        When a step succeeds
        Then a step should succeed
    """
    When I run "behat features/stop_running.feature"
    Then it should fail with:
      """
        Background:            # features/stop_running.feature:3
          Given a failing step # FeatureContext::fail()
            (Exception)

        @overlook
        Scenario: Keep Running steps # features/stop_running.feature:7
          Given a passing step       # FeatureContext::pass()
          When a step succeeds       # FeatureContext::pass()
          Then a step should succeed # FeatureContext::pass()

      """
    And the output should contain:
    """
    4 steps (1 failed, 3 skipped)
    """

  Scenario: A scenario tagged @overlook should be skipped if a BeforeScenario hook fails
    Given there is a file named "features/stop_running.feature" with:
    """
    Feature: Stop running

      @overlook @hook_fails
      Scenario: Keep Running steps
        Given a passing step
        When a step succeeds
        Then a step should succeed
    """
    When I run "behat features/stop_running.feature"
    Then it should fail with:
      """
        ┌─ @BeforeScenario @hook_fails # FeatureContext::fail()
        │
        ╳  (Exception)
        │
        @overlook @hook_fails
        Scenario: Keep Running steps # features/stop_running.feature:4
          Given a passing step       # FeatureContext::pass()
          When a step succeeds       # FeatureContext::pass()
          Then a step should succeed # FeatureContext::pass()

      """
    And the output should contain:
    """
    3 steps (3 skipped)
    """

  Scenario Outline: It uses the overlook tag defined on the configuration
    Given there is a file named "behat.yml" with:
      """
      default:
          extensions:
              bwowk\Behat\OverlookExtension:
                  overlook_tag: <tag>
      """
    And there is a file named "features/overlook_tag.feature" with:
      """
      Feature: Keep running

        @<tag>
        Scenario: Keep Running steps
          Then a step should fail
          But another step should succeed
          """
    When I run "behat features/overlook_tag.feature"
    Then it should fail with:
      """
          Then a step should fail         # FeatureContext::fail()
            (Exception)
          But another step should succeed # FeatureContext::pass()
            │ tested

      """
    And the output should contain:
      """
      2 steps (1 passed, 1 failed)
      """
    Scenarios:
    | tag     |
    | overlook  |
    | testAll |