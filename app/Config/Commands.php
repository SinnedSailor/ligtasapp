<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Commands extends BaseConfig
{
    public array $commands = [
        'documents:convert-preview' => \App\Commands\ConvertDocumentPreview::class,
    ];
}
