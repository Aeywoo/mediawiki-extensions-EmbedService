<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use Exception;
use MediaWiki\Extension\EmbedService\EmbedService;
use MediaWiki\Extension\EmbedService\EmbedService\Niconico;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\PPCustomFrame_Hash;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class NiconicoTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 'sm40807360';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = '<Foo>';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://embed.nicovideo.jp/watch/sm40807360';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://nicovideo.jp/video/40807360';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new Niconico( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Niconico::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Niconico::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new Niconico( $this->validId );

		$this->assertInstanceOf( Niconico::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Niconico::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Niconico::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Niconico( $this->validUrlId );

		$this->assertInstanceOf( Niconico::class, $service );
		$this->assertEquals( 'sm40807360', $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Niconico::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Niconico::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new Niconico( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Niconico::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Niconico::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Niconico( $this->validUrlId );

		$this->assertStringContainsString( '//embed.nicovideo.jp/watch/', $service->getUrl() );
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
