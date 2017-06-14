<?php

namespace bwowk\Behat\OverlookExtension\ServiceContainer;

use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class OverlookExtension implements Extension
{
    const STEP_CONTAINER_TESTER_ID = 'tester.step_container';
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        return;
    }

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'do_not_skip';
    }

    /**
     * Initializes other extensions.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows extensions
     * to hook into the configuration of other extensions providing such an
     * extension point.
     *
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        return;
    }

    /**
     * Setups configuration for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('overlook_tag')
                ->info('Specifies the tag used to disable "Then" steps skipping')
                ->defaultValue('overlook')
                ->end()
            ->end()
        ;
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadStepContainerTester($container, $config['overlook_tag']);
    }

    private function loadStepContainerTester(ContainerBuilder $container, $overlook_tag){
        class_alias('bwowk\Behat\OverlookExtension\Tester\StepContainerTester', 'Behat\Behat\Tester\StepContainerTester');
        $definition =  new Definition('bwowk\Behat\OverlookExtension\Tester\StepContainerTester', array(
                new Reference(TesterExtension::STEP_TESTER_ID),
                $overlook_tag
        ));
//        if ($container->hasDefinition(self::STEP_CONTAINER_TESTER_ID)){
//            $container->removeDefinition(self::STEP_CONTAINER_TESTER_ID);
//        }
        $container->setDefinition(self::STEP_CONTAINER_TESTER_ID,$definition);
    }
}
