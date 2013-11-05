<?php
return array(
	'urlFormat'=>'path',
	'rules'=>array(
		''=>'Index/Signin',
		'<c:\w+>'=>'<c>/Default',
		'<c:\w+>/<a:\w+>/*'=>'<c>/<a>',
	),
	'urlSuffix'=>'.html'
	);