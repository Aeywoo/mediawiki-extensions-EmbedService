<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\Spotify;

use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class SpotifyAlbumTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '3B61kSKTxlY36cYgzvf3cP';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://open.spotify.com/album/3B61kSKTxlY36cYgzvf3cP';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://open.spotify.com/track/123';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new SpotifyAlbum( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new SpotifyAlbum( $this->validId );

		$this->assertInstanceOf( SpotifyAlbum::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new SpotifyAlbum( $this->validUrlId );

		$this->assertInstanceOf( SpotifyAlbum::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new SpotifyAlbum( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new SpotifyAlbum( $this->validUrlId );

		$this->assertStringContainsString( 'https://open.spotify.com/embed/album/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyAlbum::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new SpotifyAlbum( $this->validUrlId );
		$this->assertEquals( 'spotify', $service->getServiceKey() );
	}
}
