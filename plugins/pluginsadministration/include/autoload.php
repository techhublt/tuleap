<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoload821242ad2590d312a247b6164b6c0bd2($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'pluginsadministration' => '/PluginsAdministration.class.php',
            'pluginsadministrationactions' => '/PluginsAdministrationActions.class.php',
            'pluginsadministrationplugin' => '/pluginsadministrationPlugin.class.php',
            'pluginsadministrationplugindescriptor' => '/PluginsAdministrationPluginDescriptor.class.php',
            'pluginsadministrationplugininfo' => '/PluginsAdministrationPluginInfo.class.php',
            'pluginsadministrationviews' => '/PluginsAdministrationViews.class.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoload821242ad2590d312a247b6164b6c0bd2');
// @codeCoverageIgnoreEnd