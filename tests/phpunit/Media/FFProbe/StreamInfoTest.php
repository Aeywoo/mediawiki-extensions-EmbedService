<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests\Media\FFProbe;

use MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo;
use MediaWikiIntegrationTestCase;

/**
 * @group EmbedService
 */
class StreamInfoTest extends MediaWikiIntegrationTestCase {

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo
	 * @return void
	 */
	public function testConstructor() {
		$info = new StreamInfo( [] );

		$this->assertInstanceOf( StreamInfo::class, $info );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getWidth
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getField
	 * @return void
	 */
	public function testGetMissingField() {
		$info = new StreamInfo( [] );

		$this->assertFalse( $info->getWidth() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getField
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getWidth
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getType
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getCodecName
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getCodecLongName
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getWidth
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getHeight
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getBitDepth
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getDuration
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getBitRate
	 * @return void
	 */
	public function testGetAllMissingFields() {
		$info = new StreamInfo( [] );

		$this->assertFalse( $info->getType() );
		$this->assertFalse( $info->getCodecName() );
		$this->assertFalse( $info->getCodecLongName() );
		$this->assertFalse( $info->getWidth() );
		$this->assertFalse( $info->getHeight() );
		$this->assertFalse( $info->getBitDepth() );
		$this->assertFalse( $info->getDuration() );
		$this->assertFalse( $info->getBitRate() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getWidth
	 * @covers \MediaWiki\Extension\EmbedService\Media\FFProbe\StreamInfo::getField
	 * @return void
	 */
	public function testGetField() {
		$info = new StreamInfo( [ 'width' => 200 ] );

		$this->assertEquals( 200, $info->getWidth() );
	}
}
