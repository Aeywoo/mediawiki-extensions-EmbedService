<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedVideo\Tests\EmbedService;

use MediaWiki\Extension\EmbedVideo\EmbedService\Wistia;
use MediaWiki\Extension\EmbedVideo\EmbedVideoException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedVideo
 */
class WistiaTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '62svuailn2';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://embed.wistia.com/iframe/62svuailn2';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://wistia.com/embed/iframe/62svuailn2';

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedVideoException::class );

		new Wistia( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new Wistia( $this->validId );

		$this->assertInstanceOf( Wistia::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Wistia( $this->validUrlId );

		$this->assertInstanceOf( Wistia::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedVideoException::class );
		new Wistia( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Wistia( $this->validUrlId );

		$this->assertStringContainsString( 'https://fast.wistia.net/embed/iframe/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new Wistia( $this->validUrlId );
		$this->assertEquals( 'wistia', $service->getServiceKey() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Wistia::getCSPUrls
	 * @return void
	 */
	public function testGetCspUrls() {
		$service = new Wistia( $this->validUrlId );
		$this->assertEquals( [
			'https://wistia.com',
			'https://fast.wistia.com',
			'https://fast.wistia.net'
		], $service->getCSPUrls() );
	}
}
