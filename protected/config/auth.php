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
    4 =>  array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Программист',
        'bizRule' => null,
        'data' => null
    ),
    5 =>  array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'UI',
        'bizRule' => null,
        'data' => null
    ),
    6 =>  array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Системный инженер',
        'bizRule' => null,
        'data' => null
    ),
    7 =>  array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Владелец компании',
        'bizRule' => null,
        'data' => null
    ),
);