<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests;

use Exception;
use MediaWiki\Extension\EmbedService\EmbedService;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Parser\PPCustomFrame_Hash;
use MediaWiki\Parser\PPFrame_Hash;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class EmbedServiceTest extends MediaWikiIntegrationTestCase {

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseArgs
	 * @return void
	 */
	public function testConstructor() {
		$ev = new EmbedService( null, [] );

		$this->assertInstanceOf( EmbedService::class, $ev );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVU
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVUYouTubeValid() {
		$parser = $this->getParser();

		$output = EmbedService::parseEVU(
			$parser,
			$this->getFrame( $parser ),
			[
				'https://youtube.com/?v=foobar',
			],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$this->assertStringContainsString(
			'<figure class="embedservice" data-service="youtube"',
			$this->resolveHtml( $parser, $output )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVU
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVUYouTubeInvalid() {
		$parser = $this->getParser();

		$output = EmbedService::parseEVU(
			$parser,
			$this->getFrame( $parser ),
			[
				'https://youtu.be/foobar',
			],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 3, $output );
		$this->assertStringNotContainsString( '<figure class="embedservice" data-service="youtube"', $output[0] );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVU
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVUEmpty() {
		$parser = $this->getParser();

		$output = EmbedService::parseEVU(
			$parser,
			$this->getFrame( $parser ),
			[],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 3, $output );
		$this->assertStringContainsString( 'errorbox', $output[0] );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVYouTubeValid() {
		$this->overrideConfigValues( [
			'EmbedServiceRequireConsent' => true,
		] );

		$parser = $this->getParser();

		$output = EmbedService::parseEV(
			$parser,
			$this->getFrame( $parser ),
			[
				'youtube',
				'https://youtube.com/?v=foobar',
			],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
        // phpcs:ignore Generic.Files.LineLength.TooLong
		$this->assertStringContainsString( '<figure class="embedservice" data-service="youtube" data-mw-iframeconfig="{&quot;src&quot;:&quot;https://www.youtube-nocookie.com/embed/foobar?autoplay=1&quot;}" style="width:640px">', $this->resolveHtml( $parser, $output ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVYouTubeValidCustomArgs() {
		$this->overrideConfigValues( [
			'EmbedServiceRequireConsent' => true,
		] );

		$parser = $this->getParser();

		$output = EmbedService::parseEV(
			$parser,
			$this->getFrame( $parser ),
			[
				'youtube',
				'https://youtube.com/?v=foobar',
				'dimensions=200x200px'
			],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$this->assertStringContainsString(
			'"width":200,"height":200',
			htmlspecialchars_decode( $this->resolveHtml( $parser, $output ) )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVDimensionHeightOnly() {
		$parser = $this->getParser();

		$output = EmbedService::parseEV(
			$parser,
			$this->getFrame( $parser ),
			[
				'youtube',
				'https://youtube.com/?v=foobar',
				'dimensions=x200px'
			],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$this->assertStringContainsString(
			'"height":200',
			htmlspecialchars_decode( $this->resolveHtml( $parser, $output ) )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVDimensionWidthOnly() {
		$parser = $this->getParser();

		$output = EmbedService::parseEV(
			$parser,
			$this->getFrame( $parser ),
			[
				'youtube',
				'https://youtube.com/?v=foobar',
				'dimensions=200px'
			],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$this->assertStringContainsString(
			'"width":200',
			htmlspecialchars_decode( $this->resolveHtml( $parser, $output ) )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVTag
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVTag() {
		$parser = $this->getParser();

		$output = EmbedService::parseEVTag(
			'',
			[
				'service' => 'youtube',
				'id' => 'https://youtube.com/?v=foobar',
			],
			$parser,
			$this->getFrame( $parser ),
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVTag
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::error
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVTagMissingService() {
		$parser = $this->getParser();

		$output = EmbedService::parseEVTag(
			'',
			[
				'id' => 'https://youtube.com/?v=foobar',
			],
			$parser,
			$this->getFrame( $parser ),
		);

		$this->assertIsArray( $output );
		$this->assertCount( 3, $output );
		$this->assertStringContainsString( wfMessage( 'embedservice-error-missingparams' )->plain(), $output[0] );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVTag
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::error
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVTagIdInInput() {
		$parser = $this->getParser();

		$output = EmbedService::parseEVTag(
			'https://youtube.com/?v=foobar',
			[
				'service' => 'youtube',
			],
			$parser,
			$this->getFrame( $parser ),
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$this->assertStringContainsString(
			'<figure class="embedservice" data-service="youtube" data-mw-iframeconfig',
			$this->resolveHtml( $parser, $output )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVTag
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @return void
	 * @throws Exception
	 */
	public function testParseArgsExample1() {
		$parser = $this->getParser();

		$output = EmbedService::parseEV(
			$parser,
			$this->getFrame( $parser ),
			[
				'youtube',
				'pSsYTj9kCHE'
			],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$this->assertStringContainsString(
			'<figure class="embedservice" data-service="youtube" data-mw-iframeconfig',
			$this->resolveHtml( $parser, $output )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVTag
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::setAlignment
	 * @return void
	 * @throws Exception
	 */
	public function testParseArgsExample2() {
		$parser = $this->getParser();

		$output = EmbedService::parseEV(
			$parser,
			$this->getFrame( $parser ),
			[
				'youtube',
				'https://www.youtube.com/watch?v=pSsYTj9kCHE',
				'1000',
				'right',
				'Example description',
				'frame',
			],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$html = $this->resolveHtml( $parser, $output );
		$this->assertStringContainsString( '"width":1000', htmlspecialchars_decode( $html ) );
		$this->assertStringContainsString( '<figcaption>Example description</figcaption>', $html );
		$this->assertStringContainsString( 'mw-halign-right', $html );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVTag
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::makeHtmlFormatConfig
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::setAlignment
	 * @return void
	 * @throws Exception
	 */
	public function testParseArgsExample6() {
		$this->overrideConfigValues( [
			'EmbedServiceRequireConsent' => true,
			'EmbedServiceShowPrivacyNotice' => true,
		] );

		$parser = $this->getParser();

		$output = EmbedService::parseEV(
			$parser,
			$this->getFrame( $parser ),
			[
				'youtube',
				'pSsYTj9kCHE',
				'dimensions=320x320',
				'title=Title of the Embed',
			],
			false
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$html = $this->resolveHtml( $parser, $output );
		$this->assertStringContainsString( '"width":320,"height":320', htmlspecialchars_decode( $html ) );
		$this->assertStringContainsString(
			'<div class="embedservice-loader__title embedservice-loader__title--manual">Title of the Embed</div>',
			$html
		);
		$this->assertStringContainsString( 'class="embedservice-privacyNotice__link"', $html );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVL
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::init
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::addModules
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getIframeConfig
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::getPrivacyPolicyUrl
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVLYouTube() {
		$parser = $this->getParser();

		$output = EmbedService::parseEVL(
			$parser,
			$this->getFrame( $parser ),
			[
				'pSsYTj9kCHE',
				'text=Test Text'
			]
		);

		$this->assertIsArray( $output );
		$this->assertCount( 3, $output );
        // phpcs:ignore Generic.Files.LineLength.TooLong
		$this->assertStringContainsString( '<a data-mw-iframeconfig="', $output[0] );
		$this->assertStringContainsString( 'Test Text', $output[0] );
	}

	/**
	 * Ensure <evlplayer> without defaultid renders a visible placeholder container
	 * using the videolink service and the correct player class.
	 *
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVLTag
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::output
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVLTagPlaceholderWithoutDefaultId() {
		$parser = $this->getParser();

		$output = EmbedService::parseEVLTag(
			'',
			[
				'id' => 'p1',
				'w' => '400',
				'h' => '200',
			],
			$parser,
			$this->getFrame( $parser ),
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$html = $this->resolveHtml( $parser, $output );
		$this->assertStringContainsString( 'class="embedservice evlplayer evlplayer-p1"', $html );
		$this->assertStringContainsString( 'data-service="videolink"', $html );
	}

	/**
	 * Ensure the explicit 'player' attribute on <evlplayer> overrides the legacy 'id'
	 * attribute as the player name, preserving backwards compatibility.
	 *
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEVLTag
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService::parseEV
	 * @return void
	 * @throws Exception
	 */
	public function testParseEVLTagPlayerAttributeOverridesId() {
		$parser = $this->getParser();

		$output = EmbedService::parseEVLTag(
			'',
			[
				'id' => 'legacy-id',
				'player' => 'explicit-player',
				'defaultid' => 'pSsYTj9kCHE',
				'service' => 'youtube',
				'w' => '400',
				'h' => '200',
			],
			$parser,
			$this->getFrame( $parser ),
		);

		$this->assertIsArray( $output );
		$this->assertCount( 2, $output );
		$this->assertStringContainsString(
			'class="embedservice evlplayer evlplayer-explicit-player"',
			$this->resolveHtml( $parser, $output )
		);
	}

	/**
	 * Resolve a nowiki strip marker returned by parseEV/parseEVU/parseEVTag in $output[0]
	 * back to the HTML it represents.
	 *
	 * @param Parser $parser
	 * @param array $output Parser function return array
	 * @return string Resolved HTML
	 */
	private function resolveHtml( Parser $parser, array $output ): string {
		return $parser->getStripState()->unstripNoWiki( $output[0] );
	}

	/**
	 * Get a fresh parser
	 *
	 * @return Parser
	 * @throws Exception
	 */
	public function getParser(): Parser {
		$parser = $this->getServiceContainer()->getParserFactory()->create();
		$parser->setOptions( ParserOptions::newFromAnon() );
		$parser->clearState();
		$parser->setOutputType( Parser::OT_HTML );

		return $parser;
	}

	/**
	 * Get a frame
	 *
	 * @param Parser|null $parser
	 * @return PPFrame_Hash
	 * @throws Exception
	 */
	private function getFrame( ?Parser $parser ): PPFrame_Hash {
		if ( $parser === null ) {
			$parser = $this->getParser();
		}

		return new PPCustomFrame_Hash( $parser->getPreprocessor(), [] );
	}
}
