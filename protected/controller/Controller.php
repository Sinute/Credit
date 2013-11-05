<?php
/**
* Controller
*/
class Controller extends BaseController
{
	const ROLE_MEMBER = 0x1;
	const ROLE_ADMIN  = 0xFFFF;

	static public function checkPrivilege($level)
	{
		return (SP::PUser()->level & $level) == $level;
	}
}
