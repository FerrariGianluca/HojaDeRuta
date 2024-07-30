<?php

require 'vendor/autoload.php';
require_once 'datos.php';
require_once 'headers.php';
require_once 'leads/hojaderuta.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\DocumentProperties;

define('LEAD_HEIGHT_CELLS', 11);

// Establece la zona horaria globalmente
date_default_timezone_set('America/Argentina/Buenos_Aires');

function makeExcel($datos, $nombre = null, $leads = [], $headers = []) {
    // Crear una nueva instancia de Spreadsheet
    $spreadsheet = new Spreadsheet();

    // Obtener la hoja activa
    $sheet = $spreadsheet->getActiveSheet();

    // Llamar a la función makeLead para configurar el encabezado
    makeLead($sheet, $leads);

    // Verifica que el array sea tridimensional
    $isThreeDimensional = isset($datos[0][0]) && is_array($datos[0][0]);
    
    // Si es tridimensional, crea una nueva hoja para cada grupo
    if ($isThreeDimensional) {
    // Itera a través de cada grupo (o hoja si solo es bidimensional)
        foreach ($datos as $groupIndex => $group) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Group ' . ($groupIndex + 1));
            $sheet->setShowGridlines(false);
            
            // Aplanar los datos de tres dimensiones a una hoja de cálculo
            $startRow = LEAD_HEIGHT_CELLS + 1;
            foreach ($group as $rowIndex => $row) {
                $column = 'A';
                foreach ($row as $cellIndex => $value) {
                    $sheets->setCellValue($column . $startRow, $value);
                    $column++;
                }
                $startRow++;
            }
        }       
    } else {  
        // Datos bidimensionales
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sheet 1');
        $sheet->setShowGridlines(false);
        $startRow = LEAD_HEIGHT_CELLS + 1;
        foreach ($datos as $groupIndex => $group) {
            // Convertir datos bidimensionales a una hoja de cálculo
            $column = 'A';
            foreach ($group as $cellIndex => $value) {
                $sheet->setCellValue($column . $startRow, $value);
                $column++;
            }
            $startRow++;
        }
    }

    // Elimina la hoja en blanco predeterminada si se creó más de una hoja
    if ($spreadsheet->getSheetCount() > 1) {
        $spreadsheet->removeSheetByIndex(0);
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

function makeLead($sheet, $lead){
    # Lead
    $sheet->setCellValue("A7", $lead['subsecretaria']);
    $sheet->setCellValue("A7", $lead['ministerio']);
    $sheet->setCellValue("D2", $lead['titulo1']);
    $sheet->mergeCells("D2:G2");
    $sheet->setCellValue("D3", $lead['titulo2']);
    $sheet->mergeCells("D3:G3");
    $sheet->setCellValue("D4", $lead['titulo3']);
    $sheet->mergeCells("D4:G4");
    $sheet->setCellValue("D6", $lead['titulo_general']);
    $sheet->mergeCells("D6:G6");
    $sheet->mergeCells("D6:D7");

    # Títulos de columnas
    $sheet->setCellValue("A11", "Nombre");
    $sheet->setCellValue("B11", "Apellido");
    $sheet->setCellValue("C11", "Edad");
    
    // Fijar las primeras $leadHeightCells filas como encabezado
    $sheet->freezePane('A' . (LEAD_HEIGHT_CELLS + 1));
}

makeExcel($datos, null, $leads, $headers);

?>
