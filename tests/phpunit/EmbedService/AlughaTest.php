<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use MediaWiki\Extension\EmbedService\EmbedService\EmbedHtmlFormatter;
use MediaWiki\Extension\EmbedService\EmbedService\EmbedServiceFactory;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 * @covers \MediaWiki\Extension\EmbedService\EmbedService\Alugha
 */
class AlughaTest extends MediaWikiIntegrationTestCase {
	private const VALID_ID = 'b8fe2460-81e1-11eb-8b27-65de6c3aea52';

	/**
	 * A bare UUID is accepted and produces the expected embed src.
	 */
	public function testValidIdProducesEmbedUrl(): void {
		$this->overrideConfigValue( 'EmbedServiceRequireConsent', true );

		$service = EmbedServiceFactory::newFromName( 'alugha', self::VALID_ID );
		$html = EmbedHtmlFormatter::toHtml( $service );

		$this->assertStringContainsString(
			'data-mw-iframeconfig="{&quot;src&quot;:&quot;https://alugha.com/embed/web-player?v='
			. self::VALID_ID . '&quot;}"', $html
		);

		// With consent enabled, no iframe is rendered until the user clicks.
		$this->assertStringNotContainsString( '<iframe', $html );
	}

	/**
	 * A full Alugha embed URL is normalised to the same video id.
	 */
	public function testFullUrlIsParsed(): void {
		$service = EmbedServiceFactory::newFromName(
			'alugha',
			'https://alugha.com/embed/web-player?v=' . self::VALID_ID
		);
		$this->assertStringContainsString(
			'https://alugha.com/embed/web-player?v=' . self::VALID_ID,
			EmbedHtmlFormatter::toHtml( $service )
		);
	}
}
