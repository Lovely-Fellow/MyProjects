<?php
$i = 0;
foreach ($components as $component):
	if ($component['component_enable'] === '1' && isset($component['options'])):
		$component_radio_button = !empty($component['component_radio']);
		require 'options/' . $component['component_type'] . '.php';
	endif;
endforeach;
