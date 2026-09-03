<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use Exception;
use MediaWiki\Extension\EmbedService\EmbedService;
use MediaWiki\Extension\EmbedService\EmbedService\NaverTV;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\PPCustomFrame_Hash;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class NaverTVTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '27831593';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = '<Foo>';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://tv.naver.com/embed/27831593';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://tv.naver.com/video/27831593';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new NaverTV( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\NaverTV::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\NaverTV::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new NaverTV( $this->validId );

		$this->assertInstanceOf( NaverTV::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\NaverTV::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\NaverTV::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new NaverTV( $this->validUrlId );

		$this->assertInstanceOf( NaverTV::class, $service );
		$this->assertSame( '27831593', $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\NaverTV::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\NaverTV::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new NaverTV( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\NaverTV::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\NaverTV::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new NaverTV( $this->validUrlId );

		$this->assertStringContainsString( '//tv.naver.com/embed/', $service->getUrl() );
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
