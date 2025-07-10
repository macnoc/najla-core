<?php

namespace Najla\Core;

class Path
{
    public static function base(): string {
        return dirname(__DIR__, 5);
    }
}