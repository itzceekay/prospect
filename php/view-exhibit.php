<!DOCTYPE html>
<meta charset="utf-8">

<head>
	<link rel="stylesheet" href="<?php echo(plugins_url('css/jquery-ui.min.css', dirname(__FILE__))); ?>" />
	<link rel="stylesheet" href="<?php echo(plugins_url('css/jquery-ui.theme.min.css', dirname(__FILE__))); ?>" />
	<link rel="stylesheet" href="<?php echo(plugins_url('lib/leaflet/leaflet.css', dirname(__FILE__))); ?>" />
	<link rel="stylesheet" href="<?php echo(plugins_url('css/view-exhibit.css', dirname(__FILE__))); ?>" />
</head>


<body>

<script src="<?php echo(includes_url('js/underscore.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/jquery.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/core.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/widget.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/mouse.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/draggable.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/position.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/resizable.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/button.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/dialog.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/slider.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/effect.min.js')); ?>"></script>
<script src="<?php echo(includes_url('js/jquery/ui/effect-slide.min.js')); ?>"></script>

<script src="<?php echo(plugins_url('lib/leaflet/leaflet.js', dirname(__FILE__))); ?>"></script>

<script src="<?php echo(plugins_url('lib/d3.js', dirname(__FILE__))); ?>"></script>

<script src="<?php echo(plugins_url('js/map-hub.js', dirname(__FILE__))); ?>"></script>
<script src="<?php echo(plugins_url('js/view-exhibit.js', dirname(__FILE__))); ?>"></script>

<script>
	var prspdata = {
		ajax_url: <?php
	global $post;

	$blog_id = get_current_blog_id();
	echo('"'.get_admin_url($blog_id ,'admin-ajax.php').'"');
?>,
		site_url: <?php
	echo('"'.site_url().'"');
?>,
		chunks: <?php
	$options = get_option('prsp_base_options');
	$chunk = isset($options['prsp_chunks']) ? (int)$options['prsp_chunks'] : 1000;
	echo $chunk;
?>,
		assets: <?
	echo('"'.plugins_url('/assets/', dirname(__FILE__)).'"');
?>,
		e: <?php
	$the_xhbt = new ProspectExhibit(true, get_the_ID(), true);
		// Put Attribute list in sorted order
	sort($the_xhbt->gen->ts);
	echo(' { id: "'.$the_xhbt->id.'", ');
	echo(' g: '.json_encode($the_xhbt->gen, JSON_UNESCAPED_UNICODE).', ');
	echo(' vf: '.$the_xhbt->meta_views.', ');
	echo(' i: '.$the_xhbt->meta_inspect.' }, ');

		// Also need to output Template data based on Exhibit data
	echo(' t: [ ');
	$first = true;
	$att_defs = array();
	foreach($the_xhbt->gen->ts as $template_id) {
		if (!$first)
			echo(', ');
		$first = false;
		$the_template = new ProspectTemplate(false, $template_id, true, true, false);
		echo('{ id: "'.$the_template->id.'", ');
		echo(' def: '.$the_template->meta_def.', ');
		echo(' n: '.$the_template->get_num_records().' }');
		$att_defs = array_merge($att_defs, $the_template->get_all_attributes(false));
	}
?> ],
		a: [ <?php

		// Get all definitions of all current Attributes
	$att_defs = ProspectAttribute::unique_sorted_att_array($att_defs);
		// Output each entry
	$first = true;
	foreach($att_defs as $the_attribute) {
			// Ignore Attributes whose Privacy setting protects them
		if ($the_attribute->privacy == 'o') {
			if (!$first)
				echo(', ');
			$first = false;
			echo('{ id: "'.$the_attribute->id.'", ');
			echo(' def: '.json_encode($the_attribute->def, JSON_UNESCAPED_UNICODE).', ');
			echo(' r: '.$the_attribute->meta_range.', ');
			echo(' l: '.json_encode($the_attribute->legend, JSON_UNESCAPED_UNICODE));
			if ($the_attribute->x != null)
				echo(', x: '.json_encode($the_attribute->x, JSON_UNESCAPED_UNICODE).' }');
			else
				echo(' }');
		}
	}
?> ],
		m: [ <?php
	$map_defs = ProspectMap::get_all_maps();
		// Output each entry
	$first = true;
	foreach($map_defs as $the_map) {
		if (!$first)
			echo(', ');
		$first = false;
		echo('{ id: "'.$the_map->id.'", ');
		echo(' sname: "'.$the_map->meta_data['sname'].'", ');
		echo(' credits: "'.$the_map->meta_data['credits'].'", ');
		echo(' url: "'.$the_map->meta_data['url'].'", ');
		echo(' subd: "'.$the_map->meta_data['subd'].'", ');
		echo(' swBounds: '.json_encode($the_map->meta_data['swBounds']).', ');
		echo(' neBounds: '.json_encode($the_map->meta_data['neBounds']).', ');
		echo(' minZoom: '.$the_map->meta_data['minZoom'].', ');
		echo(' maxZoom: '.$the_map->meta_data['maxZoom'].', ');
		echo(' inverseY: '.$the_map->meta_data['inverseY'].' }');
	}
?>	] };

</script>

<?php
		echo ProspectAdmin::get_script_text('view-exhibit.txt');
?>

</body>
</html>
