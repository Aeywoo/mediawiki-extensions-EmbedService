<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests;

use LocalFile;
use LocalRepo;
use MediaWiki\Extension\EmbedService\Media\AudioHandler;
use MediaWiki\Extension\EmbedService\Specials\SpecialRefreshEmbedServiceMetadata;
use MediaWiki\Request\FauxRequest;
use MediaWiki\SpecialPage\SpecialPage;
use RepoGroup;

/**
 * @group Database
 * @group EmbedService
 * @covers \MediaWiki\Extension\EmbedService\Specials\SpecialRefreshEmbedServiceMetadata
 */
class SpecialRefreshEmbedServiceMetadataTest extends \SpecialPageTestBase {
	private RepoGroup $repoGroup;
	private LocalRepo $localRepo;

	protected function setUp(): void {
		parent::setUp();

		$this->repoGroup = $this->createMock( RepoGroup::class );
		$this->localRepo = $this->createMock( LocalRepo::class );
		$this->repoGroup->method( 'getLocalRepo' )->willReturn( $this->localRepo );
	}

	protected function newSpecialPage(): SpecialPage {
		return new SpecialRefreshEmbedServiceMetadata(
			$this->repoGroup,
			$this->getServiceContainer()->getTitleFactory()
		);
	}

	public function testMissingTargetShowsError(): void {
		$performer = $this->getMutableTestUser()->getUser();
		$this->overrideUserPermissions( $performer, [ 'embedservice-refreshmetadata' => true ] );

		[ $html ] = $this->executeSpecialPage( '', null, 'qqx', $performer );

		$this->assertStringContainsString( 'embedservice-refreshmetadata-missing-target', $html );
	}

	public function testPostedRefreshCallsUpgradeRow(): void {
		$performer = $this->getMutableTestUser()->getUser();
		$this->overrideUserPermissions( $performer, [ 'embedservice-refreshmetadata' => true ] );

		$file = $this->createMock( LocalFile::class );
		$file->method( 'exists' )->willReturn( true );
		$file->method( 'isLocal' )->willReturn( true );
		$file->method( 'getRedirected' )->willReturn( null );
		$file->method( 'getHandler' )->willReturn( new AudioHandler() );
		$file->expects( $this->once() )->method( 'upgradeRow' );

		$this->localRepo->method( 'newFile' )->willReturn( $file );

		$request = new FauxRequest( [], true );

		[ $html ] = $this->executeSpecialPage( 'Test.ogg', $request, 'qqx', $performer );

		$this->assertStringContainsString( 'embedservice-refreshmetadata-success', $html );
	}
}
