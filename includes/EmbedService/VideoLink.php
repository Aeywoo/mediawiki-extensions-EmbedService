<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\EmbedService\EmbedService;

/**
 * Service used in empty <vplayer/> elements
 */
final class VideoLink extends AbstractEmbedService {

	/**
	 * @inheritDoc
	 */
	public function getBaseUrl(): string {
		return '';
	}
}
