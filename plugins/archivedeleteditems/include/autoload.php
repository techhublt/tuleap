<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoload9b9fda3e1a03d7272ecb772ea984701c($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'archivedeleteditemsplugin' => '/archivedeleteditemsPlugin.class.php',
            'archivedeleteditemsplugindescriptor' => '/ArchiveDeletedItemsPluginDescriptor.class.php',
            'archivedeleteditemsplugininfo' => '/ArchiveDeletedItemsPluginInfo.class.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoload9b9fda3e1a03d7272ecb772ea984701c');
// @codeCoverageIgnoreEnd