<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\YouTube;

use MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTubePlaylist;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class YouTubePlaylistTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 'PLY0KbDiiFYeNgQkjujixr7qD-FS8qecoP';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = '!Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://youtube.com/?list=PLY0KbDiiFYeNgQkjujixr7qD-FS8qecoP';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://youtube.com/embed/videoid';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new YouTubePlaylist( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTubePlaylist::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTubePlaylist::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new YouTubePlaylist( $this->validId );

		$this->assertInstanceOf( YouTubePlaylist::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTubePlaylist::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTubePlaylist::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new YouTubePlaylist( $this->validUrlId );

		$this->assertInstanceOf( YouTubePlaylist::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTubePlaylist::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTubePlaylist::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new YouTubePlaylist( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTubePlaylist::getUrl
	 * @return void
	 */
	public function testUrl() {
		$service = new YouTubePlaylist( $this->validUrlId );

		$this->assertStringContainsString( '//www.youtube-nocookie.com/embed/videoseries?list=', $service->getUrl() );
	}
}
