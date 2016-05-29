<?php
/**
 * This file is placed here for compatibility with ModuleManager.
 * It allows usage of this module even without composer.
 *
 * if we use composer :
 * "autoload" : {
        "psr-0" : {
            "\Module\Foundation\" : "src/"
    },
 *
 *  the autoloader achieve class and never fallback into Module Resolver
 */
require_once __DIR__ . '/src/Foundation/Module.php';