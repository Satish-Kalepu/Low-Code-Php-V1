
	global variables availble:
	$connection mysql data connection
	$mongodb_con  mongodb connection
	$config_global_settings     global settings 

	variable types
	String/Text  ['t'=>'T', 'v'=>'']
	Number/Float/Double  ['t'=>'N', 'v'=>1]
	List  ['t'=>'L', 'v'=>[ ['t'=>'O', 'v'=>['var'=>['t'=>'N', 'v'=>1] ] ] ] ];
	Object  ['t'=>'O', 'v'=>[ 'var'=>['t'=>'N', 'v'=>1] ] ];

	input template format 
		varname => [
			't'=>"type", 
			"m"=>"Mandatory true/false", 
			"v"=>"default value, if input not provided",
			"regexp"=>"regexp", 
			"min"=>"minimum value", 
			"max"=>"maximum value", 
			"validate_function"=> function(){

			}
		]

	function should respond in a structure
	{
		'status': 'success/fail',
		'error': '',
		'data': {structure as per output template}
	}