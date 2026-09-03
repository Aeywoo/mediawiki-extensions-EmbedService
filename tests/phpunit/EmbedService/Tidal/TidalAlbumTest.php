<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\Tidal;

use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class TidalAlbumTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '105701543';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://tidal.com/album/105701543';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://tidal.com/en/album/105701543';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new TidalAlbum( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new TidalAlbum( $this->validId );

		$this->assertInstanceOf( TidalAlbum::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new TidalAlbum( $this->validUrlId );

		$this->assertInstanceOf( TidalAlbum::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new TidalAlbum( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new TidalAlbum( $this->validUrlId );

		$this->assertStringContainsString( 'https://embed.tidal.com/albums/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalAlbum::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new TidalAlbum( $this->validUrlId );
		$this->assertEquals( 'tidal', $service->getServiceKey() );
	}
}
