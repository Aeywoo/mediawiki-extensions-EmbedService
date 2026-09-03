<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\Spotify;

use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class SpotifyTrackTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '6ZFbXIJkuI1dVNWvzJzown';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://open.spotify.com/track/6ZFbXIJkuI1dVNWvzJzown';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://open.spotify.com/album/123';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new SpotifyTrack( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new SpotifyTrack( $this->validId );

		$this->assertInstanceOf( SpotifyTrack::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new SpotifyTrack( $this->validUrlId );

		$this->assertInstanceOf( SpotifyTrack::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new SpotifyTrack( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new SpotifyTrack( $this->validUrlId );

		$this->assertStringContainsString( 'https://open.spotify.com/embed/track/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Spotify\SpotifyTrack::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new SpotifyTrack( $this->validUrlId );
		$this->assertEquals( 'spotify', $service->getServiceKey() );
	}
}
