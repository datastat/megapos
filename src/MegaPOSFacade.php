<?php

namespace Datastat\MegaPOS;

use Illuminate\Support\Facades\Facade;

class MegaPOSFacade extends Facade {
    protected static function getFacadeAccessor() { return 'megapos'; }
}
