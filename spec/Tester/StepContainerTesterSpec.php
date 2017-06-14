<?php

namespace spec\bwowk\Behat\OverlookExtension\Tester;

use Behat\Behat\Tester\Result\StepResult;
use Behat\Behat\Tester\StepTester;
use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\ExampleNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepContainerInterface;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Setup\Setup;
use Behat\Testwork\Tester\Setup\Teardown;
use bwowk\Behat\OverlookExtension\Tester\StepContainerTester;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

interface ExecutedStepResult extends StepResult {

}

class StepContainerTesterSpec extends ObjectBehavior
{
    function let(
        Environment $env,
        FeatureNode $feature,
        StepTester $tester,
        StepNode $failingGiven,
        StepNode $failingWhen,
        StepNode $failingThen,
        StepNode $passingGiven,
        StepNode $passingWhen,
        StepNode $passingThen,
        StepResult $success,
        StepResult $fail,
        StepResult $skip,
        Setup $setup,
        Teardown $tearDown
    ) {

        $failingGiven->getKeywordType()->willReturn('Given');
        $failingWhen->getKeywordType()->willReturn('When');
        $failingThen->getKeywordType()->willReturn('Then');
        $passingGiven->getKeywordType()->willReturn('Given');
        $passingWhen->getKeywordType()->willReturn('When');
        $passingThen->getKeywordType()->willReturn('Then');


        $fail->isPassed()->willReturn(false);
        $fail->getResultCode()->willReturn(\Behat\Behat\Tester\Result\ExecutedStepResult::FAILED);
        $tester->test(Argument::any(), Argument::any(), $failingGiven, false)->willReturn($fail);
        $tester->test(Argument::any(), Argument::any(), $failingWhen, false)->willReturn($fail);
        $tester->test(Argument::any(), Argument::any(), $failingThen, false)->willReturn($fail);

        $success->isPassed()->willReturn(true);
        $success->getResultCode()->willReturn(\Behat\Behat\Tester\Result\ExecutedStepResult::PASSED);
        $tester->test(Argument::any(), Argument::any(), $passingGiven, false)->willReturn($success);
        $tester->test(Argument::any(), Argument::any(), $passingWhen, false)->willReturn($success);
        $tester->test(Argument::any(), Argument::any(), $passingThen, false)->willReturn($success);

        $skip->isPassed()->willReturn(false);
        $skip->getResultCode()->willReturn(\Behat\Behat\Tester\Result\ExecutedStepResult::SKIPPED);
        $tester->test(Argument::any(), Argument::any(), Argument::any(), true)->willReturn($success);

        $setup->isSuccessful()->willReturn(true);
        $tester->setUp(Argument::any(), Argument::any(), Argument::any(), Argument::any())->willReturn($setup);
        $tearDown->isSuccessful()->willReturn(true);
        $tester->tearDown(Argument::any(), Argument::any(), Argument::any(), Argument::any(), Argument::any())->willReturn($tearDown);

    }

    function it_is_initializable($tester)
    {
        $this->beConstructedWith($tester->getWrappedObject(), 'overlook');
        $this->shouldHaveType(StepContainerTester::class);
    }

    function it_skips_after_Then_steps_on_vanilla_scenarios(
        $tester,
        $env,
        $feature,
        $passingGiven,
        $passingWhen,
        $passingThen,
        $failingThen
    ) {
        $this->beConstructedWith($tester->getWrappedObject(), 'overlook');

        $steps = [$passingGiven->getWrappedObject(), $passingWhen->getWrappedObject(), $failingThen->getWrappedObject(), $passingThen->getWrappedObject()];
        $scenario = new ScenarioNode('vanilla_scenario', array(), $steps, 'Scenario', '3');

        $this->test($env, $feature, $scenario, false);

        $tester->test($env, $feature, $passingGiven, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingWhen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $failingThen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingThen, true)->shouldHaveBeenCalled();
    }

    function it_does_not_skip_after_Then_steps_on_overlook_scenarios(
        $tester,
        $env,
        $feature,
        $passingGiven,
        $passingWhen,
        $passingThen,
        $failingThen
    ) {
        $this->beConstructedWith($tester->getWrappedObject(), 'overlook');

        $steps = [$passingGiven->getWrappedObject(), $passingWhen->getWrappedObject(), $failingThen->getWrappedObject(), $passingThen->getWrappedObject()];
        $scenario = new ScenarioNode('vanilla_scenario', array('overlook'), $steps, 'Scenario', '3');

        $this->test($env, $feature, $scenario, false);

        $tester->test($env, $feature, $passingGiven, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingWhen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $failingThen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingThen, false)->shouldHaveBeenCalled();
    }

    function it_skips_after_Then_steps_on_vanilla_examples(
        $tester,
        ExampleNode $example,
        $env,
        $feature,
        $passingGiven,
        $passingWhen,
        $passingThen,
        $failingThen
    ) {
        $this->beConstructedWith($tester->getWrappedObject(), 'overlook');

        $steps = [$passingGiven->getWrappedObject(), $passingWhen->getWrappedObject(), $failingThen->getWrappedObject(), $passingThen->getWrappedObject()];
        $example->getSteps()->willReturn($steps);
        $example->getTags()->willReturn(array());

        $this->test($env, $feature, $example->getWrappedObject(), false);

        $tester->test($env, $feature, $passingGiven, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingWhen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $failingThen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingThen, true)->shouldHaveBeenCalled();
    }

    function it_does_not_skip_after_Then_steps_on_overlook_examples(
        $tester,
        ExampleNode $example,
        $env,
        $feature,
        $passingGiven,
        $passingWhen,
        $passingThen,
        $failingThen
    ) {
        $this->beConstructedWith($tester->getWrappedObject(), 'overlook');

        $steps = [$passingGiven->getWrappedObject(), $passingWhen->getWrappedObject(), $failingThen->getWrappedObject(), $passingThen->getWrappedObject()];
        $example->getSteps()->willReturn($steps);
        $example->getTags()->willReturn(array('overlook'));

        $this->test($env, $feature, $example->getWrappedObject(), false);

        $tester->test($env, $feature, $passingGiven, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingWhen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $failingThen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingThen, false)->shouldHaveBeenCalled();
    }

    function it_skips_after_failed_Given_steps_on_overlook_containers(
        $tester,
        ScenarioInterface $container,
        $env,
        $feature,
        $failingGiven,
        $passingWhen,
        $passingThen
    ) {
        $this->beConstructedWith($tester->getWrappedObject(), 'overlook');

        $steps = [$failingGiven->getWrappedObject(), $passingWhen->getWrappedObject(), $passingThen->getWrappedObject()];
        $container->getSteps()->willReturn($steps);
        $container->getTags()->willReturn(array('overlook'));

        $this->test($env, $feature, $container->getWrappedObject(), false);

        $tester->test($env, $feature, $failingGiven, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingWhen, true)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingThen, true)->shouldHaveBeenCalled();
    }

    function it_skips_after_failed_When_steps_on_overlook_containers(
        $tester,
        ScenarioInterface $container,
        $env,
        $feature,
        $passingGiven,
        $failingWhen,
        $passingThen
    ) {
        $this->beConstructedWith($tester->getWrappedObject(), 'overlook');

        $steps = [$passingGiven->getWrappedObject(), $failingWhen->getWrappedObject(), $passingThen->getWrappedObject()];
        $container->getSteps()->willReturn($steps);
        $container->getTags()->willReturn(array('overlook'));

        $this->test($env, $feature, $container->getWrappedObject(), false);

        $tester->test($env, $feature, $passingGiven, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $failingWhen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingThen, true)->shouldHaveBeenCalled();
    }

    function it_skips_after_failed_Given_steps_on_vanilla_containers(
        $tester,
        ScenarioInterface $container,
        $env,
        $feature,
        $failingGiven,
        $passingWhen,
        $passingThen
    ) {
        $this->beConstructedWith($tester->getWrappedObject(), 'overlook');

        $steps = [$failingGiven->getWrappedObject(), $passingWhen->getWrappedObject(), $passingThen->getWrappedObject()];
        $container->getSteps()->willReturn($steps);
        $container->getTags()->willReturn(array());

        $this->test($env, $feature, $container->getWrappedObject(), false);

        $tester->test($env, $feature, $failingGiven, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingWhen, true)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingThen, true)->shouldHaveBeenCalled();
    }

    function it_skips_after_failed_When_steps_on_vanilla_containers(
        $tester,
        ScenarioInterface $container,
        $env,
        $feature,
        $passingGiven,
        $failingWhen,
        $passingThen
    ) {
        $this->beConstructedWith($tester->getWrappedObject(), 'overlook');

        $steps = [$passingGiven->getWrappedObject(), $failingWhen->getWrappedObject(), $passingThen->getWrappedObject()];
        $container->getSteps()->willReturn($steps);
        $container->getTags()->willReturn(array());

        $this->test($env, $feature, $container->getWrappedObject(), false);

        $tester->test($env, $feature, $passingGiven, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $failingWhen, false)->shouldHaveBeenCalled();
        $tester->test($env, $feature, $passingThen, true)->shouldHaveBeenCalled();
    }

}
