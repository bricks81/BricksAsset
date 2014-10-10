<?php

namespace Bricks\AssetService\LessAdapter;

interface LessAdapterInterface {

	/**
	 * @param string $source a directory/file
	 * @param string $target a directory/file
	 */
	public function less($source,$target);
	
}