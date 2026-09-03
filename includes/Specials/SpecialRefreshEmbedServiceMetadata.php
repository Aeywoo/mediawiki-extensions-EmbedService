<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\Specials;

use Exception;
use LocalFile;
use MediaWiki\Extension\EmbedService\Media\AudioHandler;
use MediaWiki\Html\Html;
use MediaWiki\HTMLForm\HTMLForm;
use MediaWiki\SpecialPage\UnlistedSpecialPage;
use MediaWiki\Status\Status;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use RepoGroup;

class SpecialRefreshEmbedServiceMetadata extends UnlistedSpecialPage {
	/**
	 * @param RepoGroup $repoGroup
	 * @param TitleFactory $titleFactory
	 */
	public function __construct(
		private RepoGroup $repoGroup,
		private TitleFactory $titleFactory
	) {
		if ( version_compare( MW_VERSION, '1.46', '>=' ) ) {
			parent::__construct( 'RefreshEmbedServiceMetadata' );
		} else {
			parent::__construct( 'RefreshEmbedServiceMetadata', 'embedservice-refreshmetadata' );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getRestriction(): string {
		return 'embedservice-refreshmetadata';
	}

	/**
	 * @return bool
	 */
	public function doesWrites(): bool {
		return true;
	}

	/**
	 * @param string|null $par
	 * @return void
	 */
	public function execute( $par ): void {
		$this->checkReadOnly();
		$this->setHeaders();
		$this->outputHeader();

		$out = $this->getOutput();
		$out->addModuleStyles( 'mediawiki.codex.messagebox.styles' );

		$title = $this->getTargetTitle( $par ?? $this->getRequest()->getText( 'target' ) );
		if ( !$title ) {
			$out->addHTML( Html::errorBox(
				$this->msg( 'embedservice-refreshmetadata-missing-target' )->escaped()
			) );
			return;
		}

		$out->addBacklinkSubtitle( $title );

		$file = $this->repoGroup->getLocalRepo()->newFile( $title );
		if ( !$this->isRefreshableFile( $file ) ) {
			$out->addHTML( Html::errorBox(
				$this->msg( 'embedservice-refreshmetadata-invalid-target' )->escaped()
			) );
			return;
		}

		$result = $this->getRefreshForm( $title, $file )->show();
		if ( $result instanceof Status && $result->isGood() ) {
			$out->addHTML( Html::successBox( (string)$result->getValue() ) );
			$out->addReturnTo( $title );
		}
	}

	/**
	 * Build the confirmation form used to refresh stored metadata.
	 *
	 * @param Title $title
	 * @param LocalFile $file
	 * @return HTMLForm
	 */
	private function getRefreshForm( Title $title, LocalFile $file ): HTMLForm {
		return HTMLForm::factory( 'ooui', [], $this->getContext() )
			->setAction( $this->getPageTitle( $title->getDBkey() )->getLocalURL() )
			->setId( 'mw-embedservice-refreshmetadata-form' )
			->setSubmitTextMsg( 'embedservice-refreshmetadata-submit' )
			->setSubmitCallback(
				function ( array $data, HTMLForm $form ) use ( $title, $file ) {
					return $this->submitRefreshMetadata( $title, $file );
				}
			)
			->addPreHtml(
				$this->msg(
					'embedservice-refreshmetadata-intro',
					$title->getPrefixedText()
				)->parseAsBlock()
			);
	}

	/**
	 * Refresh metadata for the selected local file.
	 *
	 * @param Title $title
	 * @param LocalFile $file
	 * @return Status
	 */
	private function submitRefreshMetadata( Title $title, LocalFile $file ): Status {
		try {
			$file->upgradeRow();
			return Status::newGood(
				$this->msg(
					'embedservice-refreshmetadata-success',
					$title->getPrefixedText()
				)->escaped()
			);
		} catch ( Exception $e ) {
			return Status::newFatal( 'embedservice-refreshmetadata-failed', $e->getMessage() );
		}
	}

	/**
	 * Resolve the requested file title.
	 *
	 * @param string|null $target
	 * @return Title|null
	 */
	private function getTargetTitle( ?string $target ): ?Title {
		if ( !$target ) {
			return null;
		}

		$title = $this->titleFactory->newFromText( $target, NS_FILE );
		if ( !$title || $title->getNamespace() !== NS_FILE ) {
			return null;
		}

		return $title;
	}

	/**
	 * Check whether the target file can be refreshed.
	 *
	 * @param mixed $file
	 * @return bool
	 */
	private function isRefreshableFile( mixed $file ): bool {
		return $file instanceof LocalFile
			&& $file->exists()
			&& $file->isLocal()
			&& $file->getRedirected() === null
			&& $file->getHandler() instanceof AudioHandler;
	}
}
