# Behat Overlook Extension
###### Turn a blind eye on those failing steps :see_no_evil:

**TL;DR intro:** Your steps won't be skipped after a failing one.

![Example](docs/img/example.png?raw=true)


## Installing
Do it with [composer](https://getcomposer.org/):

```bash
composer require bwowk/behat-overlook-extension
```

## Setting up
Just this will do:

```YAML
default:
    extensions:
        bwowk\Behat\OverlookExtension:
            overlook_tag: overlook
```

## Usage

Just put a @overlook tag on the scenarios where you want to prevent your `Then` steps from being skipped over after a failure:

```Gherkin
  @overlook
  Scenario: Keep Running steps
    Given a passing step
    When a step succeeds
    Then a step fails
    But the following step succeeds
    And all of them run
```

you can also put the tag above the Feature declaration so it works for all of the Scenarios in that feature:

```Gherkin
  @overlook
  Feature: Fail more
```

### But there's a catch

If there are other action steps (Given|When) after your failing Thens, they will be skipped. It wouldn't make sense to try and keep running the flow if the previous assertions failed, because the Scenario didn't reach the desired state on that part:

![Example](docs/img/skip_actions.png?raw=true)


## Motivation

The Overlook Extension makes it possible to run multiple assertions on the outcomes of a Scenario without fearing to have several steps obscured after one of them fails.
 
This helps keeping your Scenarios on the line without a whole lot of redundancy. If your `Then` steps just test the outcomes of your scenario, they shouldn't affect the state which it reached by means of their `Given` and `When` steps, so why stop there?

It was inspired by [an old goodie from Google's testing blog](https://testing.googleblog.com/2008/07/tott-expect-vs-assert.html) on the importance of seeing more failures by test.