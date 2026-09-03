<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\Qobuz;

use MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class QobuzTrackTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '359452978';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://open.qobuz.com/track/359452978';

	/**
	 * A valid widget url containing an id and explicit zone
	 * @var string
	 */
	private string $validWidgetUrlId = 'https://widget.qobuz.com/track/359452978?zone=NL-nl&display=compact';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://play.qobuz.com/album/123';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new QobuzTrack( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new QobuzTrack( $this->validId );

		$this->assertInstanceOf( QobuzTrack::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new QobuzTrack( $this->validUrlId );

		$this->assertInstanceOf( QobuzTrack::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getIdRegex
	 * @return void
	 */
	public function testValidWidgetUrlId() {
		$service = new QobuzTrack( $this->validWidgetUrlId );

		$this->assertInstanceOf( QobuzTrack::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validWidgetUrlId ) );
		$this->assertStringContainsString( 'zone=NL-nl', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new QobuzTrack( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new QobuzTrack( $this->validUrlId );

		$this->assertStringContainsString( 'https://widget.qobuz.com/track/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Qobuz\QobuzTrack::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new QobuzTrack( $this->validUrlId );
		$this->assertEquals( 'qobuz', $service->getServiceKey() );
	}
}
