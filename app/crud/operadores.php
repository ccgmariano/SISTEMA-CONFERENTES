<?php

return [
    'tabela' => 'operadores_portuarios',
    'titulo' => 'Operadores Portuários',
    'campos' => [
        'nome' => [
            'label' => 'Nome',
            'type' => 'text',
            'required' => true
        ],
        'razao_social' => [
            'label' => 'Razão Social',
            'type' => 'text'
        ],
        'cnpj' => [
            'label' => 'CNPJ',
            'type' => 'text'
        ],
        'whatsapp_group_id' => [
            'label' => 'WhatsApp Group ID',
            'type' => 'text'
        ],
        'ativo' => [
            'label' => 'Ativo',
            'type' => 'text'
        ]
    ]
];
