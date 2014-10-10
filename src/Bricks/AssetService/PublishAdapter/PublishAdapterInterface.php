<?php

namespace Bricks\AssetService\PublishAdapter;

interface PublishAdapterInterface {

	/**
	 * @param string $source the source directory/file
	 * @param string $target the target directory/file
	 */
	public function publish($source,$target);
	
}