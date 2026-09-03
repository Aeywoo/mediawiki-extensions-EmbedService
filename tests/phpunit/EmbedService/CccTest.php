<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use Exception;
use MediaWiki\Extension\EmbedService\EmbedService;
use MediaWiki\Extension\EmbedService\EmbedService\Ccc;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\PPCustomFrame_Hash;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class CccTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 'rc3-791680-introducing_utk_web_a_web_developer_s_view_on_firmware';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	// phpcs:ignore Generic.Files.LineLength.TooLong
	private string $validUrlId = 'https://media.ccc.de/v/rc3-791680-introducing_utk_web_a_web_developer_s_view_on_firmware';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Ccc::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Ccc::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new Ccc( $this->validId );

		$this->assertInstanceOf( Ccc::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Ccc::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Ccc::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Ccc( $this->validUrlId );

		$this->assertInstanceOf( Ccc::class, $service );
		$this->assertEquals(
			'rc3-791680-introducing_utk_web_a_web_developer_s_view_on_firmware',
			$service->parseVideoID( $this->validUrlId )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Ccc::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Ccc::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Ccc( $this->validUrlId );

		$this->assertStringContainsString( '//media.ccc.de/v/', $service->getUrl() );
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
			'https://media.ccc.de/v/rc3-791680-introducing_utk_web_a_web_developer_s_view_on_firmware'
		] );

		$this->assertIsArray( $out );
		$this->assertCount( 2, $out );
		$this->assertStringContainsString(
			'media.ccc.de/v/rc3-791680-introducing_utk_web_a_web_developer_s_view_on_firmware',
			$parser->getStripState()->unstripNoWiki( $out[0] )
		);
	}
}
