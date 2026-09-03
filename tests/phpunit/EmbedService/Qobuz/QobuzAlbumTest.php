<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\Qobuz;

use MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class QobuzAlbumTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 'a1nkwok5snthb';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://play.qobuz.com/album/a1nkwok5snthb';

	/**
	 * A valid widget url containing an id and explicit zone
	 * @var string
	 */
	private string $validWidgetUrlId = 'https://widget.qobuz.com/album/a1nkwok5snthb?zone=NL-nl';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://play.qobuz.com/track/123';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new QobuzAlbum( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new QobuzAlbum( $this->validId );

		$this->assertInstanceOf( QobuzAlbum::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new QobuzAlbum( $this->validUrlId );

		$this->assertInstanceOf( QobuzAlbum::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getIdRegex
	 * @return void
	 */
	public function testValidWidgetUrlId() {
		$service = new QobuzAlbum( $this->validWidgetUrlId );

		$this->assertInstanceOf( QobuzAlbum::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validWidgetUrlId ) );
		$this->assertStringContainsString( 'zone=NL-nl', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new QobuzAlbum( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new QobuzAlbum( $this->validUrlId );

		$this->assertStringContainsString( 'https://widget.qobuz.com/album/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new QobuzAlbum( $this->validUrlId );
		$this->assertEquals( 'qobuz', $service->getServiceKey() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzAlbum::getCSPUrls
	 * @return void
	 */
	public function testGetCspUrls() {
		$service = new QobuzAlbum( $this->validUrlId );
		$this->assertEquals( [ 'https://widget.qobuz.com' ], $service->getCSPUrls() );
	}
}
