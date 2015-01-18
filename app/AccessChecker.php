<?php
namespace app;

class AccessChecker implements \core\IAccessChecker {
	public function check(){
		return true;
	}
}