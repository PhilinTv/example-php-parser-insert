<?php

return [
    'rules' => [
        'insertDependency' => [
            '\App\Test\TestDependencyProvider::provide' => [
                'instanceOf' => 'My\Test\Module',
                'before' => '\App\Test\Dependencies\DependencyThree',
            ],
        ],
    ],
];
