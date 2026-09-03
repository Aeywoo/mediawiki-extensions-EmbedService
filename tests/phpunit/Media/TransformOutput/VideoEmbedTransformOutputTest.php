<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\Media\TransformOutput;

use MediaWiki\Extension\EmbedService\Media\TransformOutput\VideoEmbedTransformOutput;
use MediaWikiIntegrationTestCase;
use UnregisteredLocalFile;

/**
 * @group EmbedService
 */
class VideoEmbedTransformOutputTest extends MediaWikiIntegrationTestCase {

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\TransformOutput\VideoEmbedTransformOutput
	 * @return void
	 */
	public function testConstructor() {
		$out = new VideoEmbedTransformOutput(
			UnregisteredLocalFile::newFromPath( '/dev/null', 'image/jpeg' ),
			[]
		);

		$this->assertInstanceOf( VideoEmbedTransformOutput::class, $out );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\TransformOutput\VideoEmbedTransformOutput::toHtml
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\EmbedHtmlFormatter::toHtml
	 * @return void
	 */
	public function testToHtml() {
		$out = new VideoEmbedTransformOutput(
			UnregisteredLocalFile::newFromPath( '/dev/null', 'image/jpeg' ),
			[]
		);

		$out = $out->toHtml();

		$this->assertStringContainsString( '<video src="', $out );
		$this->assertStringContainsString( '<figure class="embedservice', $out );
		$this->assertStringContainsString( 'data-service="local-embed"', $out );
		$this->assertStringContainsString( 'embedservice--local-embed-style', $out );
		$this->assertStringNotContainsString( 'embedservice-consent', $out );
		$this->assertStringNotContainsString( 'width: px', $out );
		$this->assertStringNotContainsString( 'height: px', $out );
		$this->assertMatchesRegularExpression(
			'/<div class="embedservice-wrapper"[^>]*>.*<video\s/s',
			$out
		);
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\TransformOutput\VideoEmbedTransformOutput::toHtml
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\EmbedHtmlFormatter::makeLocalVideoEmbedStyleHtml
	 * Ensures that when core provides framing options (thumb/frame), no nested <figure>
	 * is generated — only a wrapper div with overlay + video.
	 * @return void
	 */
	public function testToHtmlWithCoreOptions() {
		$out = new VideoEmbedTransformOutput(
			UnregisteredLocalFile::newFromPath( '/dev/null', 'image/jpeg' ),
			[ 'width' => 640, 'height' => 360, 'title' => 'Test Video' ]
		);

		$html = $out->toHtml( [ 'img-class' => 'mw-file-element' ] );

		// Should NOT contain a <figure> wrapper (core provides the frame)
		$this->assertStringNotContainsString( '<figure', $html );
		// Should contain the wrapper div with embed style class
		$this->assertStringContainsString( 'embedservice-wrapper', $html );
		$this->assertStringContainsString( 'embedservice--local-embed-style', $html );
		// Should contain the local embed style overlay
		$this->assertStringContainsString( 'embedservice-localEmbedStyle', $html );
		// Should contain the video with the core-provided class
		$this->assertStringContainsString( 'mw-file-element', $html );
		$this->assertStringContainsString( '<video', $html );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\TransformOutput\VideoEmbedTransformOutput::toHtml
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\EmbedHtmlFormatter::makeLocalVideoEmbedStyleHtml
	 * @return void
	 */
	public function testToHtmlIncludesPassiveLocalEmbedStyle() {
		$out = new VideoEmbedTransformOutput(
			UnregisteredLocalFile::newFromPath( '/dev/null', 'image/jpeg' ),
			[ 'title' => 'Example local title' ]
		);

		$out = $out->toHtml();

		$this->assertStringContainsString( 'embedservice-localEmbedStyle', $out );
		$this->assertStringContainsString( 'embedservice-loader__title', $out );
		$this->assertStringContainsString( 'Example local title', $out );
		$this->assertStringContainsString( 'embedservice-loader__fakeButton', $out );
		$this->assertStringContainsString( 'embedservice-loader__service', $out );
	}
}
