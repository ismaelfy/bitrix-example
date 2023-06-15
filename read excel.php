<?php

require_once 'PHPExcel/PHPExcel.php';

class MyComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        // Obtener el nombre del archivo de Excel desde los parámetros del componente
        $excelFileName = $this->arParams['EXCEL_FILE'];

        // Crear un objeto de la clase PHPExcel
        $objPHPExcel = PHPExcel_IOFactory::load($excelFileName);

        // Obtener la hoja activa del libro de Excel
        $worksheet = $objPHPExcel->getActiveSheet();

        // Obtener el rango de celdas utilizadas en la hoja activa
        $cellRange = $worksheet->calculateWorksheetDataDimension();

        // Obtener los datos del archivo de Excel en forma de matriz
        $excelData = $worksheet->rangeToArray($cellRange);

        // Obtener las restricciones definidas en $arFields
        $restrictions = $this->arParams['ARFIELDS'];

        // Filtrar el array aplicando las restricciones
        $filteredData = $this->applyRestrictions($excelData, $restrictions);

        // Devolver el array filtrado como resultado del componente
        $this->arResult['FILTERED_DATA'] = $filteredData;

        $this->includeComponentTemplate();
    }

    private function applyRestrictions($data, $restrictions)
    {
        $filteredData = array();

        foreach ($data as $row) {
            $isValidRow = true;

            foreach ($restrictions as $restriction) {
                $column = $restriction['column'];
                $type = $restriction['type'];
                $value = $restriction['value'];

                $cellValue = $row[$this->getColumnIndex($column)];

                switch ($type) {
                    case 'equal':
                        if ($cellValue !== $value) {
                            $isValidRow = false;
                        }
                        break;

                    case 'contain':
                        if (strpos($cellValue, $value) === false) {
                            $isValidRow = false;
                        }
                        break;

                    case 'different':
                        if ($cellValue === $value) {
                            $isValidRow = false;
                        }
                        break;

                    // Agrega más casos según tus necesidades

                    default:
                        // Tipo de restricción desconocido
                        break;
                }

                if (!$isValidRow) {
                    break;
                }
            }

            if ($isValidRow) {
                $filteredData[] = $row;
            }
        }

        return $filteredData;
    }

    private function getColumnIndex($column)
    {
        return PHPExcel_Cell::columnIndexFromString($column) - 1;
    }
}

?>

<?php
/// POO class.php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

require_once $_SERVER["DOCUMENT_ROOT"]."/local/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;

class MyComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        $excelFilePath = $this->arParams['EXCEL_FILE'];
        $restrictions = $this->arParams['ARFIELDS'];

        $filteredData = $this->applyRestrictions($excelFilePath, $restrictions);

        $this->arResult['FILTERED_DATA'] = $filteredData;

        $this->includeComponentTemplate();
    }

    private function applyRestrictions($excelFilePath, $restrictions)
    {
        $filteredData = array();

        try {
            $spreadsheet = IOFactory::load($excelFilePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            for ($row = 1; $row <= $highestRow; ++$row) {
                $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                $isValidRow = true;

                foreach ($restrictions as $restriction) {
                    $column = $restriction['column'];
                    $type = $restriction['type'];
                    $value = $restriction['value'];

                    $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column) - 1;
                    $cellValue = $rowData[$columnIndex];

                    switch ($type) {
                        case 'equal':
                            if ($cellValue !== $value) {
                                $isValidRow = false;
                            }
                            break;

                        case 'contain':
                            if (strpos($cellValue, $value) === false) {
                                $isValidRow = false;
                            }
                            break;

                        case 'different':
                            if ($cellValue === $value) {
                                $isValidRow = false;
                            }
                            break;

                        // Agrega más casos según tus necesidades

                        default:
                            // Tipo de restricción desconocido
                            break;
                    }

                    if (!$isValidRow) {
                        break;
                    }
                }

                if ($isValidRow) {
                    $filteredData[] = $rowData;
                }
            }
        } catch (\Exception $e) {
            // Manejo de excepciones en caso de error al leer el archivo de Excel
        }

        return $filteredData;
    }
}


