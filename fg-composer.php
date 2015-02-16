<?php

function locateComposerFile()
{
    $filename = getenv('COMPOSER') ?: 'composer.json';
    $path = __FILE__;
    $composerFile = '';
    do {
        $path = dirname($path);
        if ($composerFile = realpath($path.'/'.$filename)) {
            break;
        }
    } while($path !== '/');

    return $composerFile;
}

function locateComposerVendors()
{
    $composerFile = locateComposerFile();
    $config = (array) loadComposerConfig($composerFile);
    if (empty($config['vendor-dir'])) {
        $composerHome = getenv('COMPOSER_HOME') ?: getenv('HOME').'/.composer';
        $configFile = '';
        if ($composerHome = realpath($composerHome)) {
            $configFile = realpath($composerHome.'/config.json');
        }
        if ($configFile) {
            $config = (array) loadComposerConfig($configFile);
        }
    }
    $vendorDir = empty($config['vendor-dir']) ? 'vendor' : $config['vendor-dir'];
    return realpath($vendorDir) ?: realpath(dirname($composerFile).'/'.$vendorDir);
}

function loadComposerConfig($file)
{
    if (!is_readable($file)) {
        return null;
    }
    $contents = json_decode(file_get_contents($file), true);
    return empty($contents['config']) ? null : $contents['config'];
}

global $composerLoader;
$composerLoader = require locateComposerVendors().'/autoload.php';
