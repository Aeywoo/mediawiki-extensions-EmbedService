<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use Exception;
use MediaWiki\Extension\EmbedService\EmbedService;
use MediaWiki\Extension\EmbedService\EmbedService\Vimeo;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\PPCustomFrame_Hash;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class VimeoTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '105035718';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = '<Foo>';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'http://vimeo.com/105035718';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://vimeo.com/videos/some-link';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new Vimeo( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vimeo::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vimeo::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new Vimeo( $this->validId );

		$this->assertInstanceOf( Vimeo::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vimeo::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vimeo::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Vimeo( $this->validUrlId );

		$this->assertInstanceOf( Vimeo::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vimeo::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vimeo::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new Vimeo( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vimeo::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vimeo::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Vimeo( $this->validUrlId );

		$this->assertStringContainsString( '//player.vimeo.com/video/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVU
	 * @return void
	 * @throws Exception
	 */
	public function testEvu(): void {
		$parser = $this->getServiceContainer()->getParser();
		$parser->setOptions( ParserOptions::newFromAnon() );
		$parser->clearState();

		$out = EmbedService::parseEVU(
			$parser, new PPCustomFrame_Hash( $parser->getPreprocessor(), [] ), [
			$this->validUrlId
		] );

		$this->assertIsArray( $out );
		$this->assertCount( 2, $out );
		$this->assertStringContainsString(
			$this->validId,
			$parser->getStripState()->unstripNoWiki( $out[0] )
		);
	}
}
