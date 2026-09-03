<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use MediaWiki\Extension\EmbedService\EmbedService\SharePoint;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class SharePointTest extends MediaWikiIntegrationTestCase {

	/**
	 * A valid url containing an id
	 * @var string
	 */
	private string $validUrlId = 'https://sub.sharepoint.com/sites/anything.mp4';

	/**
	 * An invalid url
	 * @var string
	 */
	private string $invalidUrlId = 'https://sub.sharepoint.com/anything.mp4';

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\SharePoint::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\SharePoint::getIdRegex
	 * @return void
	 */
	public function testValidUrlId() {
		$service = new SharePoint( $this->validUrlId );

		$this->assertInstanceOf( SharePoint::class, $service );
		$this->assertEquals( $this->validUrlId, $service->parseVideoID( $this->validUrlId ) );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\SharePoint::getUrlRegex
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\SharePoint::getIdRegex
	 * @return void
	 */
	public function testInvalidUrlId() {
		$this->expectException( EmbedServiceException::class );
		new SharePoint( $this->invalidUrlId );
	}
}
