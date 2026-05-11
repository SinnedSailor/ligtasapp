<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public const EMAIL_VALIDATION = 'max_length[255]|valid_email';
    public const NAME_VALIDATION = 'max_length[255]|regex_match/^([A-Za-zñÑ]+)([-\s]?[A-Za-zñÑ]+)*$/';
    public const PASS_VALIDATION = 'min_length[12]|max_length[30]|regex_match/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&~*()])(?!.*\s).+$/';
    public const CONFPASS_VALIDATION = 'max_length[255]';
    public const DATE_VALIDATION = 'max_length[255]';
    public const TEXT_VALIDATION = 'max_length[10000]';
    public const INT_VALIDATION = 'max_length[11]|integer';
    public const CONTACTNO_VALIDATION = 'max_length[20]|regex_match/^09\d{9}$/';
    public const URL_VALIDATION = 'valid_url_strict';
    public const OTP_VALIDATION = 'numeric|exact_length[6]';
    public const MAC_VALIDATION = 'max_length[255]|regex_match/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';
    public const BASIC_VALIDATION = 'max_length[255]';
}
