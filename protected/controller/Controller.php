<?php
/**
* Controller
*/
class Controller extends BaseController
{
	protected $__roles = array(
		'admin' => 0xFFFF,
		);

	protected function __checkPrivilege($level)
	{
		return (SP::PUser()->level & $level) == $level;
		// return function($user) use($level) {
		// 	return ($user->level & $level) == $level;
		// };
	}
}
