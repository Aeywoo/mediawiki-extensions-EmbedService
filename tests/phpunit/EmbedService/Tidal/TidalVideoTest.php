<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\Tidal;

use MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class TidalVideoTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '36707521';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://tidal.com/video/36707521';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://tidal.com/en/video/1234567890';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new TidalVideo( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new TidalVideo( $this->validId );

		$this->assertInstanceOf( TidalVideo::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new TidalVideo( $this->validUrlId );

		$this->assertInstanceOf( TidalVideo::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new TidalVideo( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new TidalVideo( $this->validUrlId );

		$this->assertStringContainsString( 'https://embed.tidal.com/videos/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalVideo::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new TidalVideo( $this->validUrlId );
		$this->assertEquals( 'tidal', $service->getServiceKey() );
	}
}
