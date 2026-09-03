<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests;

use MediaWiki\Api\ApiMain;
use MediaWiki\Api\ApiUsageException;
use MediaWiki\Context\RequestContext;
use MediaWiki\Extension\EmbedService\ApiEmbedService;
use MediaWiki\Tests\Api\ApiTestCase;

/**
 * @group EmbedService
 */
class ApiEmbedServiceTest extends ApiTestCase {

	/**
	 * @covers \MediaWiki\Extension\EmbedService\ApiEmbedService::getAllowedParams
	 * @return void
	 */
	public function testGetAllowedParamsIncludesVerticalAlignment() {
		$api = new ApiEmbedService( new ApiMain( new RequestContext() ), 'embedservice' );

		$this->assertArrayHasKey( 'valignment', $api->getAllowedParams() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\ApiEmbedService::execute
	 * @return void
	 * @throws ApiUsageException
	 */
	public function testYouTube() {
		$params = [
			'action' => 'embedservice',
			'service' => 'youtube',
			'id' => 'pSsYTj9kCHE',
		];

		$ret = $this->doApiRequest( $params );

		$this->assertIsArray( $ret );
		$this->assertIsArray( $ret[0] );

		$this->assertArrayHasKey( 'embedservice', $ret[0] );
		$this->assertArrayHasKey( 'html', $ret[0]['embedservice'] );

		$data = $ret[0]['embedservice']['html'];

		$this->assertStringContainsString( 'data-service="youtube"', $data );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\ApiEmbedService::execute
	 * @return void
	 * @throws ApiUsageException
	 */
	public function testYouTubeForwardsUrlArgsAndVerticalAlignment() {
		$params = [
			'action' => 'embedservice',
			'service' => 'youtube',
			'id' => 'pSsYTj9kCHE',
			'urlargs' => 'start=32',
			'valignment' => 'top',
		];

		$ret = $this->doApiRequest( $params );
		$data = $ret[0]['embedservice']['html'];

		$this->assertStringContainsString( 'start=32', $data );
		$this->assertStringContainsString( 'mw-valign-top', $data );
	}
}
