<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Tests\Loader;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;

class ClosureLoaderTest extends \PHPUnit_Framework_TestCase
{
		protected function setUp()
		{
				if (!class_exists('Symfony\Component\Config\Loader\Loader')) {
						$this->markTestSkipped('The "Config" component is not available');
				}
		}

		/**
		 * @covers Symfony\Component\DependencyInjection\Loader\ClosureLoader::supports
		 */
		public function testSupports()
		{
				$loader = new ClosureLoader(new ContainerBuilder());

				$this->assertTrue($loader->supports(function ($container) {}), '->supports() returns true if the resource is loadable');
				$this->assertFalse($loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');
		}

		/**
		 * @covers Symfony\Component\DependencyInjection\Loader\ClosureLoader::load
		 */
		public function testLoad()
		{
				$loader = new ClosureLoader($container = new ContainerBuilder());

				$loader->load(function ($container) {
						$container->setParameter('foo', 'foo');
				});

				$this->assertEquals('foo', $container->getParameter('foo'), '->load() loads a \Closure resource');
		}
}
