<?php

namespace smtech\CanvasHack;

/**
 * All exceptions thrown by CanvasHack
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasHack_Exception extends \Exception
{
    const MANIFEST = 1;
    const SQL = 2;
    const REQUIRED = 3;
    const ID = 4;
}
