<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use MediaWiki\Extension\EmbedService\EmbedService\ArchiveOrg;
use MediaWiki\Extension\EmbedService\EmbedService\EmbedServiceFactory;
use MediaWiki\Extension\EmbedService\EmbedServiceException;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class EmbedServiceFactoryTest extends MediaWikiIntegrationTestCase {

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\EmbedServiceFactory::newFromName
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\AbstractEmbedService::parseVideoID
	 * @return void
	 */
	public function testNewFromNameExists() {
		$this->assertInstanceOf(
			ArchiveOrg::class,
			EmbedServiceFactory::newFromName( 'archiveorg', 'foo' )
		);
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\EmbedServiceFactory::newFromName
	 * @return void
	 */
	public function testNewFromNameNotExists() {
		$this->expectException( EmbedServiceException::class );

		EmbedServiceFactory::newFromName( 'foo-service', '' );
	}
}
