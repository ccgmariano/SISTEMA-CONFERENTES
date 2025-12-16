<?php

return [
    'tabela' => 'navios',
    'titulo' => 'Navios',
    'campos' => [
        'nome' => [
            'label' => 'Nome',
            'type' => 'text',
            'required' => true
        ],
        'imo' => [
            'label' => 'IMO',
            'type' => 'text'
        ],
        'num_poroes' => [
            'label' => 'Número de Porões',
            'type' => 'text'
        ],
        'decks' => [
            'label' => 'Decks (ex: A,B,C)',
            'type' => 'text'
        ],
        'ativo' => [
            'label' => 'Ativo',
            'type' => 'text'
        ]
    ]
];
