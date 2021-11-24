<?php return array(
    'root' => array(
        'pretty_version' => 'dev-master',
        'version' => 'dev-master',
        'type' => 'magento2-module',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => NULL,
        'name' => 'easytransac/gateway',
        'dev' => true,
    ),
    'versions' => array(
        'easytransac/easytransac-sdk-php' => array(
            'pretty_version' => '1.3.0',
            'version' => '1.3.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../easytransac/easytransac-sdk-php',
            'aliases' => array(),
            'reference' => 'f464c796c4205a0e672fa5829ad13cfd26439583',
            'dev_requirement' => false,
        ),
        'easytransac/gateway' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'type' => 'magento2-module',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => NULL,
            'dev_requirement' => false,
        ),
    ),
);
