<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\EmbedService;

use MediaWiki\Extension\EmbedService\EmbedService\LocalVideo;
use MediaWiki\Extension\EmbedService\Media\TransformOutput\VideoTransformOutput;
use MediaWikiIntegrationTestCase;
use UnregisteredLocalFile;

/**
 * @group EmbedService
 */
class LocalVideoTest extends MediaWikiIntegrationTestCase {
	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\LocalVideo
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\LocalVideo::setTitle
	 * @return void
	 */
	public function testConstructor() {
		$service = new LocalVideo(
			new VideoTransformOutput( UnregisteredLocalFile::newFromPath( '/dev/null', 'image/jpeg' ), [] ),
			[]
		);

		$this->assertInstanceOf( LocalVideo::class, $service );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\LocalVideo
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\LocalVideo::getDefaultWidth
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\LocalVideo::getDefaultHeight
	 * @return void
	 */
	public function testGetWidthHeight() {
		$output = $this->getMockBuilder( VideoTransformOutput::class )->disableOriginalConstructor()->getMock();
		$output->expects( $this->once() )->method( 'getWidth' )->willReturn( 600 );
		$output->expects( $this->once() )->method( 'getHeight' )->willReturn( 300 );

		$service = new LocalVideo( $output, [] );

		$this->assertEquals( 600, $service->getWidth() );
		$this->assertEquals( 300, $service->getHeight() );
	}
}
