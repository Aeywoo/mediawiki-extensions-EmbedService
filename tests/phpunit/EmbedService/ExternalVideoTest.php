<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use MediaWiki\Extension\EmbedService\EmbedService\EmbedServiceFactory;
use MediaWiki\Extension\EmbedService\EmbedService\ExternalVideo;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class ExternalVideoTest extends MediaWikiIntegrationTestCase {
	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\ExternalVideo::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\ExternalVideo::getUrlRegex
	 * @return void
	 */
	public function testWhitelistedUrl() {
		$this->overrideConfigValues( [
			'AllowExternalImagesFrom' => 'foo',
		] );

		$service = EmbedServiceFactory::newFromName( 'external', 'foo' );

		$this->assertInstanceOf( ExternalVideo::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\ExternalVideo::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\ExternalVideo::getUrlRegex
	 * @return void
	 */
	public function testNonWhitelistedUrl() {
		$this->overrideConfigValues( [
			'AllowExternalImagesFrom' => 'bar',
		] );

		$this->expectException( EmbedServiceException::class );
		EmbedServiceFactory::newFromName( 'external', 'foo' );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\ExternalVideo::getBaseUrl
	 * @return void
	 */
	public function testGetBaseUrl() {
		$this->overrideConfigValues( [
			'AllowExternalImagesFrom' => 'foo',
		] );

		$service = EmbedServiceFactory::newFromName( 'external', 'foo' );

		$this->assertEmpty( $service->getBaseUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\ExternalVideo::getCSPUrls
	 * @return void
	 */
	public function testGetCspUrls() {
		$this->overrideConfigValues( [
			'AllowExternalImagesFrom' => 'foo',
		] );

		$service = EmbedServiceFactory::newFromName( 'external', 'foo' );

		$this->assertEquals( [ 'foo' ], $service->getCSPUrls() );
	}
}
