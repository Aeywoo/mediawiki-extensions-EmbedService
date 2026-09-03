<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\Tidal;

use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class TidalMixTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '00168e4895ae58cf65884d78546334';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = 'Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://tidal.com/mix/00168e4895ae58cf65884d78546334';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://tidal.com/en/mix/12051760';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new TidalMix( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new TidalMix( $this->validId );

		$this->assertInstanceOf( TidalMix::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new TidalMix( $this->validUrlId );

		$this->assertInstanceOf( TidalMix::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new TidalMix( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new TidalMix( $this->validUrlId );

		$this->assertStringContainsString( 'https://embed.tidal.com/mix/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Tidal\TidalMix::getServiceKey
	 * @return void
	 */
	public function testServiceKey() {
		$service = new TidalMix( $this->validUrlId );
		$this->assertEquals( 'tidal', $service->getServiceKey() );
	}
}
