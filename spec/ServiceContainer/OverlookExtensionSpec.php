<?php

namespace spec\bwowk\Behat\OverlookExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use bwowk\Behat\OverlookExtension\ServiceContainer\OverlookExtension;
use PhpSpec\ObjectBehavior;

class OverlookExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OverlookExtension::class);
    }

    function it_is_a_testwork_extension()
    {
        $this->shouldHaveType(Extension::class);
    }
}
