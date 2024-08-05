<?php

$datos_dos_dimensiones = [
    'encabezado' => ['#', 'ID_HRUTA', 'ID_HR', 'ROL', 'CUIL', 'AYN', 'COD_REG', 'LIT_COD_REG', 'PUESTO', 'LIT_PUESTO', 'ESTADO_AGENTE', 'DT_LAST_UPDATE', 'ID_USUARIO', 'COD_REP', 'DESC_REP', 'ACTUALIZADO_POR'],
    'datos' => [
        [21, '001012221', 1, '20-12543598-0', 'CAMPOS,ERNESTO JULIO', 83, 'Nueva Carrera Administrativa', 'SGM0102', 'Chofer de funcionario', 'Agregar destino', '2024-07-29 12:13:06', 60200120, 'SS Gestión de Recursos Humanos'],
        [21,	'001016760', 1, '20-14994185-2', 'UBIETA,HORACIO', 83, 'Nueva Carrera Administrativa', 'SGM0102', 'Chofer de funcionario', 'Agregar destino', '2024-07-29 12:13:06', 60200120, 'SS Gestión de Recursos Humanos']
    ]
];

$datos_tres_dimensiones = [
    'encabezado' => ['#', 'CUIL', 'NOMBRE Y APELLIDO', 'COD. REG.', 'LIT. COD. REG.', 'SIT. REVISTA', 'CONVOCADO', 'HORARIO', 'EXIMIDO', 'NOVEDAD', 'FIRMA'],
    'datos' => [
        [
            ['27-14120643-0', 'ALESSANDRIA,ALEJANDRA', 83, 'Nueva Carrera Administrativa', 'Activo', 'SI', '10:30-17:30', '', 'Licencia Ordinaria Con Sueldo', ''],
            ['27-28079480-0', 'AMAR,MARIA CELESTE', 83, 'Nueva Carrera Administrativa', 'Activo', 'NO', '10:00-17:00', '', '', '']
        ],
        [
            ['27-93882536-5', 'APOLAYA BALLARTA, KAREN',	65,	'Plantas Transitorias Acta 06/2014',	'Activo', 'SI',	'09:00-16:00', 'SI', '', ''],
            ['20-32199158-1', 'BORRA, SEBASTIAN',           83,	'Nueva Carrera Administrativa',         'Activo', 'SI',	'11:00-18:00', '', '', '']
        ],
        [
            ['20-31475645-3', 'CIRLA, MATIAS SANTIAGO',     65,  'Plantas Transitorias Acta 06/2014',	'Activo', 'SI',	'09:00-16:00', 'SI', '', ''],
            ['20-27737911-3', 'DELLA LATTA, LUCIANO MARIO', 83, 'Nueva Carrera Administrativa',         'Activo', 'SI',	'16:00-23:00', 'SI', '', '']	
        ]
    ]
];

?>