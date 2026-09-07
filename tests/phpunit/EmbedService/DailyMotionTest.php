<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedVideo\Tests\EmbedService;

use Exception;
use MediaWiki\Extension\EmbedVideo\EmbedService\DailyMotion;
use MediaWiki\Extension\EmbedVideo\EmbedVideo;
use MediaWiki\Extension\EmbedVideo\EmbedVideoException;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\PPCustomFrame_Hash;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedVideo
 */
class DailyMotionTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = 'xb3c5pa';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = '<Foo>';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	// phpcs:ignore Generic.Files.LineLength.TooLong
	private string $validUrlId = 'https://www.dailymotion.com/video/xb3c5pa';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://www.daily-motion.com/videos/!null';

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedVideoException::class );

		new DailyMotion( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\DailyMotion::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\DailyMotion::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new DailyMotion( $this->validId );

		$this->assertInstanceOf( DailyMotion::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\DailyMotion::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\DailyMotion::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new DailyMotion( $this->validUrlId );

		$this->assertInstanceOf( DailyMotion::class, $service );
		$this->assertEquals( 'xb3c5pa', $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\DailyMotion::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\DailyMotion::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedVideoException::class );
		new DailyMotion( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\DailyMotion::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\DailyMotion::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new DailyMotion( $this->validUrlId );

		$this->assertStringContainsString( 'https://www.dailymotion.com/embed/video/', $service->getUrl() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedVideo\EmbedVideo::parseEVU
	 * @return void
	 * @throws Exception
	 */
	public function testEvu(): void {
		$parser = $this->getServiceContainer()->getParser();
		$parser->setOptions( ParserOptions::newFromAnon() );
		$parser->clearState();

		$out = EmbedVideo::parseEVU(
			$parser, new PPCustomFrame_Hash( $parser->getPreprocessor(), [] ), [
			$this->validUrlId
		] );

		$this->assertIsArray( $out );
		$this->assertCount( 2, $out );
		$this->assertStringContainsString(
			'xb3c5pa',
			$parser->getStripState()->unstripNoWiki( $out[0] )
		);
	}
}
