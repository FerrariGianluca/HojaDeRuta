<?php

require 'vendor/autoload.php';
require_once 'datos.php';
require_once 'headers.php';
require_once 'leads/hojaderuta.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\DocumentProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Color; 

define('LEAD_HEIGHT_CELLS', 11);

// Establece la zona horaria globalmente
date_default_timezone_set('America/Argentina/Buenos_Aires');

function makeExcel($datos, $nombre = null, $leads = [], $headers = []) {
    // Crear una nueva instancia de Spreadsheet
    $spreadsheet = new Spreadsheet();

    // Obtener la hoja activa
    $sheet = $spreadsheet->getActiveSheet();

    // Verifica que el array sea tridimensional
    $isThreeDimensional = isset($datos['datos'][0][0]) && is_array($datos['datos'][0][0]);

    $title = strtolower($leads['titulo_general']);
    $title = ucfirst($title);

    // Si es tridimensional, crea una nueva hoja para cada grupo
    if ($isThreeDimensional) {
    // Itera a través de cada grupo (o hoja si solo es bidimensional)
        foreach ($datos['datos'] as $groupIndex => $group) {
            $sheet = $spreadsheet->createSheet();
            makeLead($datos, $sheet, $leads, $groupIndex+1);
            $sheet->setTitle($title . ' ' . ($groupIndex + 1));
            $sheet->setShowGridlines(false);
            // Aplanar los datos de tres dimensiones a una hoja de cálculo
            $startRow = LEAD_HEIGHT_CELLS+1;
            $num=1;
            foreach ($group as $rowIndex => $row) {
                $sheet->getStyle($startRow.':'.$startRow)->getFont()->setSize(10);
                $sheet->getStyle($startRow.':'.$startRow)->getFont()->setName('Arial');
                $sheet->getStyle($startRow.':'.$startRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle($startRow.':'.$startRow)->getBorders()->getBottom()->setColor(new Color('FF000000'));
                $sheet->getRowDimension($startRow)->setRowHeight(32);
                $sheet->setCellValue('A' . $startRow, $num);
                $column = 'B';
                $num++;
                foreach ($row as $cellIndex => $value) {
                    $sheet->setCellValue($column . $startRow, $value);
                    $column++;
                }


                $startRow++;
            }
            $highestColumn = $sheet->getHighestColumn();
            $highestRow = $sheet->getHighestRow();
            $highestCell = $highestColumn . $highestRow;
            $sheet->getStyle('A' . LEAD_HEIGHT_CELLS+1 . ':' . $highestCell)->getFont()->setSize(10);
            $sheet->getStyle('A' . LEAD_HEIGHT_CELLS+1 . ':' .$highestCell)->getFont()->setName('Arial');
        }       
    } else {  
        // Datos bidimensionales
        $sheet = $spreadsheet->getActiveSheet();
        makeLead($datos, $sheet, $leads, 1);
        $sheet->setTitle($title);
        $sheet->setShowGridlines(false);
        $startRow = LEAD_HEIGHT_CELLS+1;
        $num=1;
        foreach ($datos['datos'] as $rowIndex => $row) {
            // Convertir datos bidimensionales a una hoja de cálculo
            $sheet->getStyle($startRow.':'.$startRow)->getFont()->setSize(10);
            $sheet->getStyle($startRow.':'.$startRow)->getFont()->setName('Arial');
            $sheet->getStyle($startRow.':'.$startRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($startRow.':'.$startRow)->getBorders()->getBottom()->setColor(new Color('FF000000'));            
            $sheet->getRowDimension($startRow)->setRowHeight(32);
            $sheet->setCellValue('A' . $startRow, $num);
            $column = 'B';
            $num++;
            foreach ($row as $cellIndex => $value) {
                $sheet->setCellValue($column . $startRow, $value);
                $column++;
            }
            $startRow++;
        }
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $highestCell = $highestColumn . $highestRow;
        $sheet->getStyle('A' . LEAD_HEIGHT_CELLS+1 . ':' . $highestCell)->getFont()->setSize(10);
        $sheet->getStyle('A' . LEAD_HEIGHT_CELLS+1 . ':' .$highestCell)->getFont()->setName('Arial');
    }

    // Elimina la hoja en blanco predeterminada si se creó más de una hoja
    if ($spreadsheet->getSheetCount() > 1) {
        $spreadsheet->removeSheetByIndex(0);
        $spreadsheet->setActiveSheetIndex(0);
    }

    // Establecer las propiedades del documento
    $properties = $spreadsheet->getProperties();
    $properties->setTitle($headers['titulo'])
               ->setSubject($headers['asunto'])
               ->setCreator($headers['creador'])
               //->setLastModifiedBy($headers['modificador'])
               ->setDescription($headers['descripcion'])
               ->setKeywords($headers['palabras_clave'])
               ->setCategory($headers['categoria']);

    // Establecer el nombre del archivo
    if ($nombre === null) {
        // Obtiene la parte de microsegundos como un entero de 6 dígitos
        $microseconds = sprintf('%06d', (int)(microtime(true) * 1000000) % 1000000);
        
        // Genera el nombre del archivo con fecha, hora y microsegundos
        $nombre = date('Y_m_d_H_i_s_') . $microseconds . '.xlsx'; // Nombre por defecto con fecha y hora
    } else {
        $nombre .= '.xlsx';
    }

    // Crear un escritor de archivo Excel
    $writer = new Xlsx($spreadsheet);

    // Guardar el archivo
    $writer->save($nombre);

    echo "Archivo Excel creado exitosamente como '$nombre'!";
}


// Función para convertir centímetros a píxeles
function cmToPixels($cm) {
    return $cm * 37.8;
}

function makeLead($datos, $sheet, $lead, $id){
    # Lead

    # Nueva instancia de Drawing para el logo
    $drawing = new Drawing();
    # Nombre
    $drawing->setName('Logo');
    # Descripcion
    $drawing->setDescription($lead['imagen']['description']);
    # Ruta
    $drawing->setPath($lead['imagen']['url']); 
    # Altura
    $drawing->setHeight(cmToPixels(2.78));
    # Ancho
    $drawing->setWidth(cmToPixels(6.38));
    # Posición
    $drawing->setCoordinates('B1'); 
    # Desplazamiento X en píxeles
    // $drawing->setOffsetX(10);
    # Desplazamiento Y en píxeles
    // $drawing->setOffsetY(10);
    # Añadir el logo a la hoja de cálculo
    $drawing->setWorksheet($sheet);

    # Agregado del lead
    $sheet->setCellValue("A7", $lead['subsecretaria']);
    $sheet->setCellValue("A9", $lead['ministerio']);
    $sheet->setCellValue("D2", $lead['titulo1']);
    $sheet->mergeCells("D2:G2");
    $sheet->setCellValue("D3", $lead['titulo2']);
    $sheet->mergeCells("D3:G3");
    $sheet->setCellValue("D4", $lead['titulo3']);
    $sheet->mergeCells("D4:G4");
    $sheet->setCellValue("D6", $lead['titulo_general']);
    $sheet->mergeCells("D6:G7");
    $sheet->setCellValue("J2", "FECHA");
    $sheet->mergeCells("J2:K2");
    $sheet->setCellValue("J3", date('d').'-'.date('m').'-'.date('Y'));
    $sheet->mergeCells("J3:K4");
    $sheet->setCellValue("J6", $lead['tipo']);
    $sheet->mergeCells("J6:K6");
    $sheet->setCellValue("J7", $id);
    $sheet->mergeCells("J7:K8");

    # Títulos de columnas
    $column=$datos['encabezado'];
    $columnIndex='A';
    $rowIndex=11;
    foreach($column as $title){
       $sheet->setCellValue($columnIndex.$rowIndex, $title);
       $columnIndex++;
    };

    // $sheet->setCellValue("A11", "Nombre");
    // $sheet->setCellValue("B11", "Apellido");
    // $sheet->setCellValue("C11", "Edad");
    
    # Estilos

    $borderTopMedium = [
        'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000'],
                ],
        ]
    ];
    $borderBottomMedium = [
        'borders' => [
            'bottom' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
            ],
        ]
    ];
    $borderLeftMedium = [
        'borders' => [
            'left' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
            ],
        ]
    ];
    $borderRightMedium = [
        'borders' => [
            'right' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
            ],
        ]
    ];
    $borderTopThin = [
        'borders' => [
            'top' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ]
    ];
    $borderBottomThin = [
        'borders' => [
            'bottom' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ]
    ];
    $borderLeftThin = [
        'borders' => [
            'left' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ]
    ];
    $borderRightThin = [
        'borders' => [
            'right' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ]
    ];

    $sheet->getStyle('11:11')->applyFromArray($borderTopThin);

    $highestColumn = $sheet->getHighestColumn();
    $highestRow = $sheet->getHighestRow();
    $highestCell = $highestColumn . $highestRow;
    $sheet->getStyle('A1:' . $highestCell)->getFont()->setSize(10);
    $sheet->getStyle('A1:' .$highestCell)->getFont()->setName('Arial');

    # Título 1
    $sheet->getStyle('D2')->getFont()->setBold(true);
    $sheet->getStyle('D2')->getFont()->setSize(12);
    $sheet->getStyle('D2')->getFont()->setName('Arial');
    $sheet->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    # Título 2
    $sheet->getStyle('D3')->getFont()->setBold(true);
    $sheet->getStyle('D3')->getFont()->setSize(12);
    $sheet->getStyle('D3')->getFont()->setName('Arial');
    $sheet->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    # Título 3
    $sheet->getStyle('D4')->getFont()->setBold(true);
    $sheet->getStyle('D4')->getFont()->setSize(12);
    $sheet->getStyle('D4')->getFont()->setName('Arial');
    $sheet->getStyle('D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    # Título General
    $sheet->getStyle('D6')->getFont()->setBold(true);
    $sheet->getStyle('D6')->getFont()->setSize(18);
    $sheet->getStyle('D6')->getFont()->setName('Arial');
    $sheet->getStyle('D6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('D6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

    # Subsecretaría
    $sheet->getStyle('A7')->getFont()->setSize(10);
    $sheet->getStyle('A7')->getFont()->setName('Arial');

    # Ministerio
    $sheet->getStyle('A9')->getFont()->setSize(10);
    $sheet->getStyle('A9')->getFont()->setName('Arial');

    # Fecha titulo
    $sheet->getStyle('J2')->getFont()->setBold(true);
    $sheet->getStyle('J2')->getFont()->setSize(12);
    $sheet->getStyle('J2')->getFont()->setName('Arial');
    $sheet->getStyle('J2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('J2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('J2')->applyFromArray($borderLeftMedium);
    $sheet->getStyle('J2')->applyFromArray($borderTopMedium);
    $sheet->getStyle('J2')->applyFromArray($borderBottomThin);
    $sheet->getStyle('K2')->applyFromArray($borderRightMedium);
    $sheet->getStyle('K2')->applyFromArray($borderTopMedium);
    $sheet->getStyle('K2')->applyFromArray($borderBottomThin);

    # Fecha
    $sheet->getStyle('J3')->getFont()->setBold(true);
    $sheet->getStyle('J3')->getFont()->setSize(18);
    $sheet->getStyle('J3')->getFont()->setName('Arial');
    $sheet->getStyle('J3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('J3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('J3')->applyFromArray($borderLeftMedium);
    $sheet->getStyle('J4')->applyFromArray($borderLeftMedium);
    $sheet->getStyle('J4')->applyFromArray($borderBottomMedium);
    $sheet->getStyle('K3')->applyFromArray($borderRightMedium);
    $sheet->getStyle('K4')->applyFromArray($borderRightMedium);
    $sheet->getStyle('K4')->applyFromArray($borderBottomMedium);

    # Tipo titulo
    $sheet->getStyle('J6')->getFont()->setBold(true);
    $sheet->getStyle('J6')->getFont()->setSize(12);
    $sheet->getStyle('J6')->getFont()->setName('Arial');
    $sheet->getStyle('J6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('J6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('J6')->applyFromArray($borderLeftMedium);
    $sheet->getStyle('J6')->applyFromArray($borderTopMedium);
    $sheet->getStyle('J6')->applyFromArray($borderBottomThin);
    $sheet->getStyle('K6')->applyFromArray($borderRightMedium);
    $sheet->getStyle('K6')->applyFromArray($borderTopMedium);
    $sheet->getStyle('K6')->applyFromArray($borderBottomThin);

    # Tipo
    $sheet->getStyle('J7')->getFont()->setBold(true);
    $sheet->getStyle('J7')->getFont()->setSize(18);
    $sheet->getStyle('J7')->getFont()->setName('Arial');
    $sheet->getStyle('J7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('J7')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('J7')->applyFromArray($borderLeftMedium);
    $sheet->getStyle('J8')->applyFromArray($borderLeftMedium);
    $sheet->getStyle('J8')->applyFromArray($borderBottomMedium);
    $sheet->getStyle('K7')->applyFromArray($borderRightMedium);
    $sheet->getStyle('K8')->applyFromArray($borderRightMedium);
    $sheet->getStyle('K8')->applyFromArray($borderBottomMedium);

    # Anchos de columnas
    $sheet->getColumnDimension('A')->setWidth(4.22);
    $sheet->getColumnDimension('B')->setWidth(18.22);
    $sheet->getColumnDimension('C')->setWidth(39.22);
    $sheet->getColumnDimension('D')->setWidth(14.22);
    $sheet->getColumnDimension('E')->setWidth(29.22);
    $sheet->getColumnDimension('F')->setWidth(17.22);
    $sheet->getColumnDimension('G')->setWidth(16.22);
    $sheet->getColumnDimension('H')->setWidth(12.22);
    $sheet->getColumnDimension('I')->setWidth(12.22);
    $sheet->getColumnDimension('J')->setWidth(19.22);
    $sheet->getColumnDimension('K')->setWidth(18.22);

    # Altos de columnas
    $sheet->getRowDimension(7)->setRowHeight(23);

    # Fijar las primeras $leadHeightCells filas como encabezado
    $sheet->freezePane('A' . (LEAD_HEIGHT_CELLS + 1));
}

makeExcel($datos_dos_dimensiones, 'prueba_dos_dimensiones', $leads, $headers);
echo "<br>";
makeExcel($datos_tres_dimensiones, 'prueba_tres_dimensiones', $leads, $headers);

?>
