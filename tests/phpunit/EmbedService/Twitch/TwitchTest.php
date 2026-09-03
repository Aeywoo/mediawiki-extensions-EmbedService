<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\Spotify;

use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class TwitchTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '012-foo-123';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = '!Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://twitch.tv/012-foo-123';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://twitch.tv/!vid#eo';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new Twitch( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new Twitch( $this->validId );

		$this->assertInstanceOf( Twitch::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Twitch( $this->validUrlId );

		$this->assertInstanceOf( Twitch::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new Twitch( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Twitch\Twitch::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Twitch( $this->validUrlId );

		$this->assertStringContainsString( 'https://player.twitch.tv/?channel=', $service->getUrl() );
		$this->assertStringContainsString( 'parent=', $service->getUrl() );
	}
}
