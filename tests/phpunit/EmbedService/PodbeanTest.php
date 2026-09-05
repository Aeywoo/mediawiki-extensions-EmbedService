<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedVideo\Tests\EmbedService;

use MediaWiki\Extension\EmbedVideo\EmbedService\Podbean;
use MediaWiki\Extension\EmbedVideo\EmbedVideoException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedVideo
 */
class PodbeanTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 're9b6-13cbd09-pb';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://www.podbean.com/player-v2/?i=re9b6-13cbd09-pb';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://www.podbean.com/player-v2/?i=Foo-Bar';

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedVideoException::class );

		new Podbean( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new Podbean( $this->validId );

		$this->assertInstanceOf( Podbean::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Podbean( $this->validUrlId );

		$this->assertInstanceOf( Podbean::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedVideoException::class );
		new Podbean( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Podbean( $this->validUrlId );

		$this->assertStringContainsString( 'https://www.podbean.com/player-v2/?i=', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new Podbean( $this->validUrlId );
		$this->assertEquals( 'podbean', $service->getServiceKey() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Podbean::getCSPUrls
	 * @return void
	 */
	public function testGetCspUrls() {
		$service = new Podbean( $this->validUrlId );
		$this->assertEquals( [ 'https://www.podbean.com' ], $service->getCSPUrls() );
	}
}
