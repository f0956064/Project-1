<?php

return [

	/*
	    |--------------------------------------------------------------------------
	    | View Storage Paths
	    |--------------------------------------------------------------------------
	    |
	    | Most templating systems load templates from disk. Here you may specify
	    | an array of paths that should be checked for your views. Of course
	    | the usual Laravel view path has already been registered for you.
	    |
*/

	'paths' => [
		resource_path('views'),
	],

	/*
	    |--------------------------------------------------------------------------
	    | Compiled View Path
	    |--------------------------------------------------------------------------
	    |
	    | This option determines where all the compiled Blade templates will be
	    | stored for your application. Typically, this is within the storage
	    | directory. However, as usual, you are free to change this value.
	    |
*/

	'compiled' => env(
		'VIEW_COMPILED_PATH',
		realpath(storage_path('framework/views'))
	),

	'buttons' => [
		'primary' => 'btn btn-danger waves-effect',
		'secondary' => 'btn btn-dark waves-effect',
	],
	'table' => [
		'table_class' => 'table project-list-table table-nowrap align-middle table-borderless',
		'table_head_class' => 'table-light sticky-head',
		'list_light_button' => 'btn btn-sm btn-outline-secondary waves-effect',
		'list_danger_button' => 'btn btn-sm btn-outline-danger waves-effect',
	],
];
