<?php

namespace bwowk\Behat\OverlookExtension\Tester;

use Behat\Behat\Tester\StepTester;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Gherkin\Node\StepContainerInterface;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\IntegerTestResult;
use Behat\Testwork\Tester\Result\TestWithSetupResult;

class StepContainerTester
{
    private $stepTester;

    private $overlook_tag;


    function __construct(StepTester $stepTester, $overlook_tag)
    {
        $this->stepTester = $stepTester;
        $this->overlook_tag = $overlook_tag;
    }

    public function test(Environment $env, FeatureNode $feature, StepContainerInterface $container, $skip)
    {
        $overlook = false;
        if ($container instanceof ScenarioInterface){
            $overlook = in_array($this->overlook_tag,array_merge($container->getTags(), $feature->getTags()));
        }

        $results = array();
        $skipNextAction = false;
        foreach ($container->getSteps() as $step) {
            $isActionStep = $step->getKeywordType() != 'Then';
            $skip = $overlook && $skipNextAction && $isActionStep || $skip;

            $setup = $this->stepTester->setUp($env, $feature, $step, $skip);
            $skipSetup = !$setup->isSuccessful() || $skip;

            $testResult = $this->stepTester->test($env, $feature, $step, $skipSetup);
            $stepNotSuccessful = !$testResult->isPassed();

            $skipNextAction = $stepNotSuccessful || $skipNextAction;
            $skip = $overlook && $isActionStep && $stepNotSuccessful  || !$overlook && $stepNotSuccessful || $skip;

            $teardown = $this->stepTester->tearDown($env, $feature, $step, $skipSetup, $testResult);
            $skip = $skip || $skipSetup || !$teardown->isSuccessful();

            $integerResult = new IntegerTestResult($testResult->getResultCode());
            $results[] = new TestWithSetupResult($setup, $integerResult, $teardown);
        }

        return $results;
    }

}