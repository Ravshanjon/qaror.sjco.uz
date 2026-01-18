<?php

return [
    /*
    |--------------------------------------------------------------------------
    | File Upload Limits
    |--------------------------------------------------------------------------
    |
    | Maximum file sizes for uploads (in kilobytes)
    |
    */
    'max_pdf_size' => env('QAROR_MAX_PDF_SIZE', 20480), // 20MB default
    'max_excel_size' => env('QAROR_MAX_EXCEL_SIZE', 10240), // 10MB default

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Default number of items per page
    |
    */
    'items_per_page' => env('QAROR_ITEMS_PER_PAGE', 25),

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Maximum string lengths for validation
    |
    */
    'max_title_length' => 255,
    'max_search_query_length' => 255,
];
