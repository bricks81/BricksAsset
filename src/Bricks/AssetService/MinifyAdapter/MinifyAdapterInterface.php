<?php

namespace Bricks\AssetService\MinifyAdapter;

interface MinifyAdapterInterface {
	
	/**
	 * @param string $source a directory/file
	 * @param string $target a directory/file
	 */
	public function minifyCss($source,$target);
	
	/**
	 * @param string $source a directory/file
	 * @param string $target a directory/file
	 */
	public function minifyJs($source,$target);
	
}