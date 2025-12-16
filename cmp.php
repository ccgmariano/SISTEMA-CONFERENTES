<?php

return [
    'tabela' => 'motivos_paralisacao',
    'titulo' => 'Motivos de Paralisação',
    'campos' => [
        'nome' => [
            'label' => 'Motivo',
            'type' => 'text',
            'required' => true
        ],
        'ativo' => [
            'label' => 'Ativo',
            'type' => 'text'
        ]
    ]
];
