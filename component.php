<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\Response\DataType\Page;
use Bitrix\Main\Error;
use Bitrix\Main\UI\PageNavigation;

class MyComponent extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [
            'getFilteredData' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod(
                        [ActionFilter\HttpMethod::METHOD_GET]
                    ),
                    new ActionFilter\Csrf(),
                ],
            ],
        ];
    }

    public function getFilteredDataAction($excelFilePath, $restrictions, $pageNumber = 1, $pageSize = 10)
    {
        $filteredData = $this->applyRestrictions($excelFilePath, $restrictions);

        $totalCount = count($filteredData);

        // Paginación
        $pageNavigation = new PageNavigation('page');
        $pageNavigation->allowAllRecords(true)
            ->setPageSize($pageSize)
            ->setRecordCount($totalCount)
            ->setCurrentPage($pageNumber);

        $filteredData = array_slice($filteredData, ($pageNumber - 1) * $pageSize, $pageSize);

        return new Page('filteredData', $filteredData, $pageNavigation);
    }

    private function applyRestrictions($excelFilePath, $restrictions)
    {
        $filteredData = [];

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFilePath);
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
            $this->addError(new Error('Error al procesar el archivo de Excel.'));
        }

        return $filteredData;
    }
}
