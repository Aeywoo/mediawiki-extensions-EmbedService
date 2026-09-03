<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedVideo\Tests\EmbedService;

use MediaWiki\Extension\EmbedVideo\EmbedService\Youku;
use MediaWiki\Extension\EmbedVideo\EmbedVideoException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedVideo
 */
class YoukuTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 'XMzc0Mzg4NTE5Mg';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://player.youku.com/embed/XMzc0Mzg4NTE5Mg';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://player.youku.com/XMzc0Mzg4NTE5Mg';

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedVideoException::class );

		new Youku( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new Youku( $this->validId );

		$this->assertInstanceOf( Youku::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Youku( $this->validUrlId );

		$this->assertInstanceOf( Youku::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedVideoException::class );
		new Youku( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Youku( $this->validUrlId );

		$this->assertStringContainsString( 'https://player.youku.com/embed/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new Youku( $this->validUrlId );
		$this->assertEquals( 'youku', $service->getServiceKey() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\Youku::getCSPUrls
	 * @return void
	 */
	public function testGetCspUrls() {
		$service = new Youku( $this->validUrlId );
		$this->assertEquals( [ 'https://youku.com', 'https://player.youku.com' ], $service->getCSPUrls() );
	}
}
