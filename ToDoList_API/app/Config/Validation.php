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
    public static function rulesStore(){
        return [
            'title' => 'required|max_length[50]|is_unique[tasks.title]',
            'description' => 'max_length[150]',
            'checked' => 'required|in_list[0,1]',
        ];
    }

    // Mensagens de validação da API
    public static function messagesStore(){
        return [
            'title' => [
                'required' => 'O campo Título é obrigatório.',
                'max_length' => 'O Título pode ter no máximo 50 caracteres.',
                'is_unique' => 'Já existe um dado com esse nome. Escolha um título único.',
            ],
            'description' => [
                'max_length' => 'A Descrição pode ter no máximo 150 caracteres.',
            ],
            'checked' => [
                'required' => 'O campo Checked é obrigatório.',
                'in_list' => 'O campo Checked só aceita valores 0 ou 1.',
            ],
        ];
    }

    // Regras de validação da API
    public static function rulesUpdate(){
        return [
            'title' => 'required|max_length[50]',
            'description' => 'max_length[150]',
            'checked' => 'required|in_list[0,1]',
        ];
    }

    // Mensagens de validação da API
    public static function messagesUpdate(){
        return [
            'title' => [
                'required' => 'O campo Título é obrigatório.',
                'max_length' => 'O Título pode ter no máximo 50 caracteres.',
            ],
            'description' => [
                'max_length' => 'A Descrição pode ter no máximo 150 caracteres.',
            ],
            'checked' => [
                'required' => 'O campo Checked é obrigatório.',
                'in_list' => 'O campo Checked só aceita valores 0 ou 1.',
            ],
        ];
    }
    

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
}