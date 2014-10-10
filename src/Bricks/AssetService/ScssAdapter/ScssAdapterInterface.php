<?php

namespace Bricks\AssetService\ScssAdapter;

interface ScssAdapterInterface {

	/**
	 * @param string $source a directory/file
	 * @param string $target a directory/file
	 */
	public function scss($source,$target);
	
}