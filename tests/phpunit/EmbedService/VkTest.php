<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use Exception;
use MediaWiki\Extension\EmbedService\EmbedService;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWiki\Extension\EmbedService\EmbedService\Vk;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\PPCustomFrame_Hash;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class VkTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid ID
	 * @var string
	 */
	private string $validId = '-22822305_456241864';

	/**
	 * An invalid id
	 * @var string
	 */
	private string $invalidId = '<Foo>';

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://vkvideo.ru/video-22822305_456241864';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://dev.vk.com/en/widgets/video';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testInvalidId() {
		$this->expectException( EmbedServiceException::class );

		new Vk( $this->invalidId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vk::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vk::getIdRegex
	 * @return void
	 */
	public function testValidId() {
		$service = new Vk( $this->validId );

		$this->assertInstanceOf( Vk::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vk::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vk::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new Vk( $this->validUrlId );

		$this->assertInstanceOf( Vk::class, $service );
		$this->assertSame( '-22822305', $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vk::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vk::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new Vk( $this->invalidUrlId );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getUrl
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vk::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Vk::getIdRegex
	 * @return void
	 */
	public function testUrl() {
		$service = new Vk( $this->validUrlId );

		$this->assertStringContainsString( 'https://vkvideo.ru/video_ext', $service->getUrl() );
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
			'oid=-22822305&id=456241864',
			htmlspecialchars_decode( $parser->getStripState()->unstripNoWiki( $out[0] ) )
		);
	}
}
