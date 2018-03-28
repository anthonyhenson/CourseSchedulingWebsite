<?php
	$cssVersion = cssVersion();
	function cssVersion()
	{
		$version = "";
		for ($i = 0; $i < 3; $i++)
		{
			//$index = rand(1,9);
			$version .= rand(1,9);
			if ($i < 2)
				$version .= '.';
		}
		return $version;
	}
?>