<?php
namespace core;

class Layout extends View {
	public function render($data = array()){
		echo parent::render($data);
	}
}