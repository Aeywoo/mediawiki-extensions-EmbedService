<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService\YouTube;

use Exception;
use MediaWiki\Extension\EmbedService\EmbedService;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\PPCustomFrame_Hash;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class YouTubeTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 'pSsYTj9kCHE';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = '!Foo-Bar';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://youtube.com/?v=pSsYTj9kCHE';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://youtube.com/embed/videoid';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new YouTube( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new YouTube( $this->validId );

		$this->assertInstanceOf( YouTube::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new YouTube( $this->validUrlId );

		$this->assertInstanceOf( YouTube::class, $service );
		$this->assertEquals( $this->validId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new YouTube( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new YouTube( $this->validUrlId );

		$this->assertStringContainsString( '//www.youtube-nocookie.com/embed/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\YouTube\YouTube::getIdRegex
	 * @return void
	 */
	public function testShortUrl() {
		$service = new YouTube( 'https://youtu.be/0123video' );

		$this->assertStringContainsString( '//www.youtube-nocookie.com/embed/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::__toString
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\EmbedHtmlFormatter::makeIframe
	 * @return void
	 */
	public function testToString() {
		$this->overrideConfigValues( [
			'EmbedServiceRequireConsent' => false,
		] );

		$service = new YouTube( 'https://youtu.be/0123video' );

		$this->assertStringContainsString( '<iframe', (string)$service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::__toString
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\EmbedHtmlFormatter::makeIframe
	 * @return void
	 */
	public function testToStringEmptyOnConsent() {
		$this->overrideConfigValues( [
			'EmbedServiceRequireConsent' => true,
		] );

		$service = new YouTube( 'https://youtu.be/0123video' );

		$this->assertEmpty( (string)$service );
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
