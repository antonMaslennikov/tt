<?php

return array(
    0 => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Гость',
        'bizRule' => null,
        'data' => null
    ),
    1 => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'менеджер',
        'children' => array(
            0, // унаследуемся от гостя
        ),
        'bizRule' => null,
        'data' => null
    ),
	2 => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Старший менеджер',
        'children' => array(
            1, // унаследуемся от менеджера
        ),
        'bizRule' => null,
        'data' => null
    ),
    3 => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Администратор',
        'children' => array(
            2, // унаследуемся от менеджера
        ),
        'bizRule' => null,
        'data' => null
    ),
);