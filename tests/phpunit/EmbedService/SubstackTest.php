<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use MediaWiki\Extension\EmbedService\EmbedService\Substack;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class SubstackTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 'gregreese';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://gregreese.substack.com/p/recent-study-shows-self-assembly';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://substack.com/p/foo';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Substack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Substack::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Substack( $this->validUrlId );

		$this->assertInstanceOf( Substack::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Substack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Substack::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new Substack( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Substack::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Substack::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Substack( $this->validUrlId );

		$this->assertStringContainsString( '//gregreese.substack.com/embed/p', $service->getUrl() );
	}
}
