<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use Exception;
use MediaWiki\Extension\EmbedService\EmbedService;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Extension\EmbedService\EmbedService\Bilibili;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\PPCustomFrame_Hash;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class BilibiliTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 'BV1Hz4y1k7ae';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = '<Foo>';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://player.bilibili.com/player.html?bvid=1Hz4y1k7ae&amp;page=1';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://bilibili.com/foobar';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new Bilibili( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Bilibili::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Bilibili::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new Bilibili( $this->validId );

		$this->assertInstanceOf( Bilibili::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Bilibili::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Bilibili::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Bilibili( $this->validUrlId );

		$this->assertInstanceOf( Bilibili::class, $service );
		$this->assertEquals( '1Hz4y1k7ae', $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Bilibili::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Bilibili::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new Bilibili( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Bilibili::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Bilibili::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Bilibili( $this->validUrlId );

		$this->assertStringContainsString( '//player.bilibili.com/player.html?bvid=', $service->getUrl() );
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
			'1Hz4y1k7ae',
			$parser->getStripState()->unstripNoWiki( $out[0] )
		);
	}
}
