<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

require_once $_SERVER["DOCUMENT_ROOT"] . "/local/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;

class MyComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult['FILTERED_DATA'] = $this->getFilteredData();

        $this->includeComponentTemplate();
    }

    private function getFilteredData()
    {
        $excelFilePath = $this->arParams['EXCEL_FILE'];
        $restrictions = $this->arParams['ARFIELDS'];

        $filteredData = $this->applyRestrictions($excelFilePath, $restrictions);

        return $filteredData;
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
                $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false)[0];

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
