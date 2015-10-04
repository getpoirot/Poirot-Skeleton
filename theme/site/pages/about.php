<?php
/** @var \Poirot\View\Interpreter\IsoRenderer $this */

## change variables
switch($request_page) {
    case 'about': $request_page = 'Page About Company History';
        break;
}

## using renderer features
$this->addMethod('date', function() {
    return date('Y-m-d H:i:s');
});
