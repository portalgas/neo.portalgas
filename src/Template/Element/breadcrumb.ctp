<?php
echo '<section class="content-header">';
echo '<h1>'.$label.'<small>'.$action.'</small></h1>';

echo '<ol class="breadcrumb">';
echo '<li>';
echo '<a href="/admin"><i class="fa fa-dashboard"></i>'; 
echo __('Home');
echo '</a>';
echo '</li>';
if(!empty($breadcrumbs))
foreach($breadcrumbs as $breadcrumb) {

	$url = [];
	if(isset($breadcrumb['controller']))
		$url['controller'] = $breadcrumb['controller'];
	$url['action'] = $breadcrumb['action'];

	$ico = '';
	if(isset($breadcrumb['ico']))
		$ico = '<i class="'.$breadcrumb['ico'].'"></i> ';

	$breadcrumb_label = $ico.$breadcrumb['label'];

	echo '<li>';
	echo $this->Html->link($breadcrumb_label, $url, ['title' => $breadcrumb['label']]);
	echo '</li>';	
}

echo '<li>'.$action.'</li>';

echo '</ol>';
echo '</section>';