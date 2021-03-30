<?php

return [
    [
        'name' => 'Vendorproductunits',
        'flag' => 'vendorproductunits.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'vendorproductunits.create',
        'parent_flag' => 'vendorproductunits.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'vendorproductunits.edit',
        'parent_flag' => 'vendorproductunits.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'vendorproductunits.destroy',
        'parent_flag' => 'vendorproductunits.index',
    ],
];
