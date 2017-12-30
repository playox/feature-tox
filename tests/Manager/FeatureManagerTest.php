<?php

namespace Flagception\Tests\Listener;

use Flagception\Activator\ArrayActivator;
use Flagception\Activator\FeatureActivatorInterface;
use Flagception\Decorator\ArrayDecorator;
use Flagception\Manager\FeatureManager;
use Flagception\Manager\FeatureManagerInterface;
use Flagception\Model\Context;
use PHPUnit\Framework\TestCase;

/**
 * Class FeatureManagerTest
 *
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 * @package Flagception\Tests\Listener
 */
class FeatureManagerTest extends TestCase
{
    /**
     * Test implement interface
     *
     * @return void
     */
    public function testImplementInterface()
    {
        $manager = new FeatureManager(
            new ArrayActivator(),
            new ArrayDecorator()
        );

        static::assertInstanceOf(FeatureManagerInterface::class, $manager);
    }

    /**
     * Test feature not active
     *
     * @return void
     */
    public function testFeatureNotActive()
    {
        $manager = new FeatureManager(
            new ArrayActivator(),
            new ArrayDecorator()
        );

        static::assertFalse($manager->isActive('feature_foo'));
    }

    /**
     * Test feature is active without context
     *
     * @return void
     */
    public function testFeatureActiveWithoutContext()
    {
        $manager = new FeatureManager(
            new ArrayActivator(['feature_foo']),
            new ArrayDecorator()
        );

        static::assertTrue($manager->isActive('feature_foo'));
    }

    /**
     * Test feature is active with context
     *
     * @return void
     */
    public function testFeatureActiveWithContext()
    {
        $manager = new FeatureManager(
            $activator = $this->createMock(FeatureActivatorInterface::class),
            new ArrayDecorator()
        );

        $context = new Context();
        $context->add('user_id', 23);
        $context->add('role', 'ROLE_ADMIN');

        $activator
            ->expects(static::once())
            ->method('isActive')
            ->with('feature_foo', $context)
            ->willReturn(true);

        static::assertTrue($manager->isActive('feature_foo', $context));
    }
}