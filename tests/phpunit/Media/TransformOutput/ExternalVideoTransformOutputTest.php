<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\Media\TransformOutput;

use MediaWiki\Extension\EmbedService\Media\TransformOutput\ExternalVideoTransformOutput;
use UnregisteredLocalFile;

/**
 * @group EmbedService
 */
class ExternalVideoTransformOutputTest extends \MediaWikiIntegrationTestCase {

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\TransformOutput\ExternalVideoTransformOutput
	 * @return void
	 */
	public function testConstructor() {
		$out = new ExternalVideoTransformOutput(
			UnregisteredLocalFile::newFromPath( '/dev/null', 'image/jpeg' ),
			[]
		);

		$this->assertInstanceOf( ExternalVideoTransformOutput::class, $out );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\TransformOutput\ExternalVideoTransformOutput::setUrl
	 * @return void
	 */
	public function testSetUrl() {
		$out = new ExternalVideoTransformOutput(
			UnregisteredLocalFile::newFromPath( '/dev/null', 'image/jpeg' ),
			[]
		);

		$out->setUrl( 'foo-url' );

		$this->assertEquals( 'foo-url', $out->getUrl() );
	}

}
