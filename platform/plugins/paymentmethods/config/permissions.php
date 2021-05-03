<?php

return [
    [
        'name' => 'Paymentmethods',
        'flag' => 'paymentmethods.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'paymentmethods.create',
        'parent_flag' => 'paymentmethods.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'paymentmethods.edit',
        'parent_flag' => 'paymentmethods.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'paymentmethods.destroy',
        'parent_flag' => 'paymentmethods.index',
    ],
];
