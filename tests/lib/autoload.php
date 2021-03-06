<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoload9db16ca2e4cda180a915c1284095c13a($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'databaseinitialization' => '/DatabaseInitialisation.class.php',
            'dbtestaccess' => '/DBTestAccess.php',
            'fakedataaccessresult' => '/TestHelper.class.php',
            'ongoingintelligentstub' => '/MockBuilder.php',
            'restbase' => '/rest/RestBase.php',
            'test\\rest\\requestwrapper' => '/rest/RequestWrapper.php',
            'test\\rest\\tracker\\tracker' => '/rest/tracker/Tracker.php',
            'test\\rest\\tracker\\trackerfactory' => '/rest/tracker/TrackerFactory.php',
            'testdatabuilder' => '/rest/TestDataBuilder.php',
            'testhelper' => '/TestHelper.class.php',
            'tuleapdbtestcase' => '/TuleapDbTestCase.class.php',
            'tuleaptestcase' => '/TuleapTestCase.class.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoload9db16ca2e4cda180a915c1284095c13a');
// @codeCoverageIgnoreEnd