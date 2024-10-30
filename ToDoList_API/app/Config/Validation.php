<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig{
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

    // Regras de validação da API
    public static function rules(){
        return [
            'title' => 'required|max_length[50]',
            'description' => 'max_length[150]',
        ];
    }

    // Mensagens de validação da API
    public static function messages(){
        return [
            'title' => [
                'required' => 'O campo Título é obrigatório.',
                'max_length' => 'O Título pode ter no máximo 50 caracteres.',
            ],
            'description' => [
                'max_length' => 'A Descrição pode ter no máximo 150 caracteres.',
            ],
        ];
    }

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
}