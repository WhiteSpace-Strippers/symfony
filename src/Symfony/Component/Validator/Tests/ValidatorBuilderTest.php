<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests;

use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Validator\ValidatorBuilderInterface;

class ValidatorBuilderTest extends \PHPUnit_Framework_TestCase
{
		/**
		 * @var ValidatorBuilderInterface
		 */
		protected $builder;

		protected function setUp()
		{
				$this->builder = new ValidatorBuilder();
		}

		protected function tearDown()
		{
				$this->builder = null;
		}

		public function deprecationErrorHandler($errorNumber, $message, $file, $line, $context)
		{
				if ($errorNumber & E_USER_DEPRECATED) {
						return true;
				}

				return \PHPUnit_Util_ErrorHandler::handleError($errorNumber, $message, $file, $line);
		}

		public function testAddObjectInitializer()
		{
				$this->assertSame($this->builder, $this->builder->addObjectInitializer(
						$this->getMock('Symfony\Component\Validator\ObjectInitializerInterface')
				));
		}

		public function testAddObjectInitializers()
		{
				$this->assertSame($this->builder, $this->builder->addObjectInitializers(array()));
		}

		public function testAddXmlMapping()
		{
				$this->assertSame($this->builder, $this->builder->addXmlMapping('mapping'));
		}

		public function testAddXmlMappings()
		{
				$this->assertSame($this->builder, $this->builder->addXmlMappings(array()));
		}

		public function testAddYamlMapping()
		{
				$this->assertSame($this->builder, $this->builder->addYamlMapping('mapping'));
		}

		public function testAddYamlMappings()
		{
				$this->assertSame($this->builder, $this->builder->addYamlMappings(array()));
		}

		public function testAddMethodMapping()
		{
				$this->assertSame($this->builder, $this->builder->addMethodMapping('mapping'));
		}

		public function testAddMethodMappings()
		{
				$this->assertSame($this->builder, $this->builder->addMethodMappings(array()));
		}

		public function testEnableAnnotationMapping()
		{
				if (!class_exists('Doctrine\Common\Annotations\AnnotationReader')) {
						$this->markTestSkipped('Annotations is required for this test');
				}

				$this->assertSame($this->builder, $this->builder->enableAnnotationMapping());
		}

		public function testDisableAnnotationMapping()
		{
				$this->assertSame($this->builder, $this->builder->disableAnnotationMapping());
		}

		public function testSetMetadataFactory()
		{
				set_error_handler(array($this, "deprecationErrorHandler"));
				$this->assertSame($this->builder, $this->builder->setMetadataFactory(
						$this->getMock('Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface'))
				);
				restore_error_handler();
		}

		public function testSetMetadataCache()
		{
				$this->assertSame($this->builder, $this->builder->setMetadataCache(
						$this->getMock('Symfony\Component\Validator\Mapping\Cache\CacheInterface'))
				);
		}

		public function testSetConstraintValidatorFactory()
		{
				$this->assertSame($this->builder, $this->builder->setConstraintValidatorFactory(
						$this->getMock('Symfony\Component\Validator\ConstraintValidatorFactoryInterface'))
				);
		}

		public function testSetTranslator()
		{
				$this->assertSame($this->builder, $this->builder->setTranslator(
						$this->getMock('Symfony\Component\Translation\TranslatorInterface'))
				);
		}

		public function testSetTranslationDomain()
		{
				$this->assertSame($this->builder, $this->builder->setTranslationDomain('TRANS_DOMAIN'));
		}
}
