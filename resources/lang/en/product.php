<?php 

return [
    'index' => [
        'title' => 'Product',
        'page_title' => 'Product',
        'page_title_desc' => '',
        'panel' => [
            'list_panel' => [
                'title' => 'Product Lists',
            ],
            'crud_panel' => [
                'title_create' => 'Create Product',
                'title_show' => 'Show Product',
                'title_edit' => 'Edit Product',
            ],
        ],
        'table' => [
            'product_list' => [
                'header' => [
                    'type' => 'Type',
                    'name' => 'Name',
                    'short_code' => 'Short Code',
                    'description' => 'Description',
                    'status' => 'Status',
                    'remarks' => 'Remarks',
                ],
            ],
        ],
    ],
    'fields' => [
        'type' => 'Type',
        'category' => 'Category',
        'name' => 'Name',
        'short_code' => 'Short Code',
        'description' => 'Description',
        'unit' => 'Unit',
        'barcode' => 'Barcode',
        'minimal_in_stock' => 'Minimal In Stock',
        'status' => 'Status',
        'remarks' => 'Remarks',
    ],
];