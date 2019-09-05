<?php
/**
 * @file Bootstrapping File for Test Suite
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */

$envs_path = __DIR__ . '/../envs.local.php';
if (is_file($envs_path))
{
   include $envs_path;
}

$loader_path = __DIR__ . '/../vendor/autoload.php';
$loader = include $loader_path;
$loader->add('', __DIR__);
