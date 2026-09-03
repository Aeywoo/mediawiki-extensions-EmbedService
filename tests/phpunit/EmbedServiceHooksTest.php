<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Tests;

use Exception;
use LocalFile;
use LocalRepo;
use MediaWiki\Context\RequestContext;
use MediaWiki\Extension\EmbedService\EmbedService\EmbedServiceFactory;
use MediaWiki\Extension\EmbedService\EmbedServiceHooks;
use MediaWiki\Extension\EmbedService\Media\AudioHandler;
use MediaWiki\Output\OutputPage;
use MediaWikiIntegrationTestCase;
use RepoGroup;

/**
 * @group EmbedService
 */
class EmbedServiceHooksTest extends MediaWikiIntegrationTestCase {
	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedServiceHooks
	 * @return void
	 * @throws Exception
	 */
	public function testConstructor() {
		$hooks = new EmbedServiceHooks(
			$this->getServiceContainer()->getConfigFactory(),
			$this->getServiceContainer()->getRepoGroup()
		);

		$this->assertInstanceOf( EmbedServiceHooks::class, $hooks );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedServiceHooks::onBeforePageDisplay
	 * @return void
	 * @throws Exception
	 */
	public function testNotAddModules() {
		$this->overrideConfigValues( [
			'EmbedServiceUseEmbedStyleForLocalVideos' => false,
		] );

		$hooks = new EmbedServiceHooks(
			$this->getServiceContainer()->getConfigFactory(),
			$this->getServiceContainer()->getRepoGroup()
		);

		$page = new OutputPage( RequestContext::getMain() );

		$hooks->onBeforePageDisplay( $page, $page->getSkin() );

		$this->assertEmpty( $page->getModules() );
		$this->assertEmpty( $page->getModuleStyles() );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedServiceHooks::onBeforePageDisplay
	 * @return void
	 * @throws Exception
	 */
	public function testAddModules() {
		$this->overrideConfigValues( [
			'EmbedServiceUseEmbedStyleForLocalVideos' => true,
		] );

		$hooks = new EmbedServiceHooks(
			$this->getServiceContainer()->getConfigFactory(),
			$this->getServiceContainer()->getRepoGroup()
		);

		$page = new OutputPage( RequestContext::getMain() );

		$hooks->onBeforePageDisplay( $page, $page->getSkin() );

		$this->assertNotEmpty( $page->getModules() );
		$this->assertNotEmpty( $page->getModuleStyles() );

		$this->assertEquals( 'ext.embedService.overlay', $page->getModules()[0] );
		$this->assertEquals( 'ext.embedService.styles', $page->getModuleStyles()[0] );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedServiceHooks::setup
	 * @return void
	 */
	public function testAddAudioHandler() {
		global $wgMediaHandlers;

		$this->overrideConfigValues( [
			'EmbedServiceEnableAudioHandler' => true,
		] );

		EmbedServiceHooks::setup();

		$this->assertEquals( AudioHandler::class, $wgMediaHandlers['audio/ogg'] );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedServiceHooks::setup
	 * @return void
	 */
	public function testNotAddAudioHandler() {
		$this->markTestSkipped( 'Can\'t disable extension setup function' );

		global $wgMediaHandlers, $wgEmbedServiceEnableAudioHandler;

		$this->setMwGlobals( [
			'$wgEmbedServiceEnableAudioHandler' => false,
		] );
		$wgEmbedServiceEnableAudioHandler = false;

		EmbedServiceHooks::setup();

		$this->assertNotEquals( AudioHandler::class, $wgMediaHandlers['audio/ogg'] );
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedServiceHooks::onParserFirstCallInit
	 * @return void
	 * @throws Exception
	 */
	public function testAddFunctionHooks() {
		$hooks = new EmbedServiceHooks(
			$this->getServiceContainer()->getConfigFactory(),
			$this->getServiceContainer()->getRepoGroup()
		);

		$parser = $this->getServiceContainer()->getParser();

		$hooks->onParserFirstCallInit( $parser );

		$names = array_map( fn( $service ) => $service::getServiceName(), EmbedServiceFactory::getAvailableServices() );
		$tags = $parser->getTags();

		foreach ( $names as $service ) {
			$this->assertContains( $service, $tags );
		}
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedServiceHooks::onParserFirstCallInit
	 * @covers \MediaWiki\Extension\EmbedService\EmbedService\EmbedServiceFactory::getAvailableServices
	 * @return void
	 * @throws Exception
	 */
	public function testAddFunctionHooksPartial() {
		$this->overrideConfigValues( [
			'EmbedServiceEnabledServices' => [
				'youtube'
			]
		] );

		$hooks = new EmbedServiceHooks(
			$this->getServiceContainer()->getConfigFactory(),
			$this->getServiceContainer()->getRepoGroup()
		);

		$parser = $this->getServiceContainer()->getParser();

		$hooks->onParserFirstCallInit( $parser );

		$names = array_map( fn( $service ) => $service::getServiceName(), EmbedServiceFactory::getAvailableServices() );
		$tags = $parser->getTags();

		foreach ( $names as $service ) {
			if ( $service === 'youtube' ) {
				$this->assertContains( $service, $tags );
			} else {
				$this->assertNotContains( $service, $tags );
			}
		}
	}

	/**
	 * @covers \MediaWiki\Extension\EmbedService\EmbedServiceHooks::onSkinTemplateNavigation__Universal
	 */
	public function testAddsRefreshMetadataActionForLocalFile(): void {
		$title = $this->getServiceContainer()->getTitleFactory()->newFromText( 'Test.ogg', NS_FILE );
		$user = new class {
			public function isAllowed( string $permission ): bool {
				return $permission === 'embedservice-refreshmetadata';
			}
		};

		$file = $this->createMock( LocalFile::class );
		$file->method( 'exists' )->willReturn( true );
		$file->method( 'isLocal' )->willReturn( true );
		$file->method( 'getRedirected' )->willReturn( null );
		$file->method( 'getHandler' )->willReturn( new AudioHandler() );

		$localRepo = $this->createMock( LocalRepo::class );
		$localRepo->method( 'newFile' )->with( $title )->willReturn( $file );

		$repoGroup = $this->createMock( RepoGroup::class );
		$repoGroup->method( 'getLocalRepo' )->willReturn( $localRepo );

		$message = new class {
			public function text(): string {
				return 'Refresh metadata';
			}
		};

		$skin = new class( $title, $user, $message ) {
			public function __construct(
				private $title,
				private $user,
				private $message
			) {
			}

			public function getTitle() {
				return $this->title;
			}

			public function getUser() {
				return $this->user;
			}

			public function msg( string $key ) {
				if ( $key !== 'embedservice-refreshmetadata-tab' ) {
					throw new \InvalidArgumentException( "Unexpected message key: $key" );
				}

				return $this->message;
			}
		};

		$hooks = new EmbedServiceHooks(
			$this->getServiceContainer()->getConfigFactory(),
			$repoGroup
		);

		$links = [ 'actions' => [] ];
		$hooks->onSkinTemplateNavigation__Universal( $skin, $links );

		$this->assertArrayHasKey( 'embedservice-refreshmetadata', $links['actions'] );
		$this->assertStringContainsString(
			'Special:RefreshEmbedServiceMetadata/Test.ogg',
			$links['actions']['embedservice-refreshmetadata']['href']
		);
	}

}
