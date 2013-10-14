<?php
return array(
	'urlFormat'=>'path',
	'rules'=>array(
		''=>'Index/Default',
		'<c:\w+>'=>'<c>/Default',
		'<c:\w+>/<a:\w+>/*'=>'<c>/<a>',
	),
	'urlSuffix'=>'.html'
	);