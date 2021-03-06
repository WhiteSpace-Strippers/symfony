<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BinaryFileResponseTest extends \PHPUnit_Framework_TestCase
{
		public function testConstruction()
		{
				$response = new BinaryFileResponse('README.md', 404, array('X-Header' => 'Foo'), true, null, true, true);
				$this->assertEquals(404, $response->getStatusCode());
				$this->assertEquals('Foo', $response->headers->get('X-Header'));
				$this->assertTrue($response->headers->has('ETag'));
				$this->assertTrue($response->headers->has('Last-Modified'));
				$this->assertFalse($response->headers->has('Content-Disposition'));

				$response = BinaryFileResponse::create('README.md', 404, array(), true, ResponseHeaderBag::DISPOSITION_INLINE);
				$this->assertEquals(404, $response->getStatusCode());
				$this->assertFalse($response->headers->has('ETag'));
				$this->assertEquals('inline; filename="README.md"', $response->headers->get('Content-Disposition'));
		}

		/**
		 * @expectedException \LogicException
		 */
		public function testSetContent()
		{
				$response = new BinaryFileResponse('README.md');
				$response->setContent('foo');
		}

		public function testGetContent()
		{
				$response = new BinaryFileResponse('README.md');
				$this->assertFalse($response->getContent());
		}

		public function testRequests()
		{
				$response = BinaryFileResponse::create(__DIR__.'/File/Fixtures/test.gif')->setAutoEtag();

				// do a request to get the ETag
				$request = Request::create('/');
				$response->prepare($request);
				$etag = $response->headers->get('ETag');

				// prepare a request for a range of the testing file
				$request = Request::create('/');
				$request->headers->set('If-Range', $etag);
				$request->headers->set('Range', 'bytes=1-4');

				$this->expectOutputString('IF8');
				$response = clone $response;
				$response->prepare($request);
				$response->sendContent();

				$this->assertEquals('binary', $response->headers->get('Content-Transfer-Encoding'));
		}

		public function testXSendfile()
		{
				$request = Request::create('/');
				$request->headers->set('X-Sendfile-Type', 'X-Sendfile');

				BinaryFileResponse::trustXSendfileTypeHeader();
				$response = BinaryFileResponse::create('README.md');
				$response->prepare($request);

				$this->expectOutputString('');
				$response->sendContent();

				$this->assertContains('README.md', $response->headers->get('X-Sendfile'));
		}

		/**
		 * @dataProvider getSampleXAccelMappings
		 */
		public function testXAccelMapping($realpath, $mapping, $virtual)
		{
				$request = Request::create('/');
				$request->headers->set('X-Sendfile-Type', 'X-Accel-Redirect');
				$request->headers->set('X-Accel-Mapping', $mapping);

				$file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
										 ->disableOriginalConstructor()
										 ->getMock();
				$file->expects($this->any())
						 ->method('getRealPath')
						 ->will($this->returnValue($realpath));
				$file->expects($this->any())
						 ->method('isReadable')
						 ->will($this->returnValue(true));

				BinaryFileResponse::trustXSendFileTypeHeader();
				$response = new BinaryFileResponse('README.md');
				$reflection = new \ReflectionObject($response);
				$property = $reflection->getProperty('file');
				$property->setAccessible(true);
				$property->setValue($response, $file);

				$response->prepare($request);
				$this->assertEquals($virtual, $response->headers->get('X-Accel-Redirect'));
		}

		public function getSampleXAccelMappings()
		{
				return array(
						array('/var/www/var/www/files/foo.txt', '/files/=/var/www/', '/files/var/www/files/foo.txt'),
						array('/home/foo/bar.txt', '/files/=/var/www/,/baz/=/home/foo/', '/baz/bar.txt'),
				);
		}
}
