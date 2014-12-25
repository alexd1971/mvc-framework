<?php

namespace app\views;

use core\MVCF;
class Matrix extends \core\View {
	
	public function __construct() {
		
		parent::__construct();
		
		$app = MVCF::app();
		$app->registerAssets(array(
				"document_ready_function" => array(
						"type" => "javascript",
						"text" => <<<SCRIPT
$(document).ready(function() { 
 if( ! $('#myCanvas').tagcanvas({
    textColour : '#ffffff',
    outlineThickness : 1,
    maxSpeed : 0.03,
    depth : 0.75
  })) {
    // TagCanvas failed to load
    $('#myCanvasContainer').hide();
  }

  if( ! $('#myCanvas2').tagcanvas({
    textColour : '#ffffff',
    outlineThickness : 1,
    maxSpeed : 0.03,
    depth : 0.75
  })) {
    // TagCanvas failed to load
    $('#myCanvasContainer').hide();
  }

  if( ! $('#myCanvas3').tagcanvas({
    textColour : '#ffffff',
    outlineThickness : 1,
    maxSpeed : 0.03,
    depth : 0.75
  })) {
    // TagCanvas failed to load
    $('#myCanvasContainer').hide();
  }

  if( ! $('#myCanvas4').tagcanvas({
    textColour : '#ffffff',
    outlineThickness : 1,
    maxSpeed : 0.03,
    depth : 0.75
  })) {
    // TagCanvas failed to load
    $('#myCanvasContainer').hide();
  }
});
SCRIPT
				)
		));
		
		$app->addAssets(array("jquery.tagcanvas", "document_ready_function"));
	}
}