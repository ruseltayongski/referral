<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsultationReportExport implements FromArray, WithHeadings, WithStyles
{
    
    protected $data;
    protected $headings;

    public function __construct(array $data, array $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }
    
    public function array(): array{
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }   

    /**
     * Apply styles (borders) to all cells with data.
     */
    public function styles(Worksheet $sheet)
    {
        // Loop through each row and apply borders only to non-empty rows
        $maxCol = 0;
        // Define color map for section headers and table headers
        $sectionHeaderColor = '36b9cc'; // teal
        $departmentHeaderColor = '4e73df'; // blue
        $statisticsHeaderColor = '1cc88a'; // green
        $ageHeaderColor = 'f6c23e'; // yellow
        $whiteText = 'FFFFFF';
        $blackText = '000000';
        // Gender Distribution column header colors (match dashboard/chart style)
        $genderStatsHeader = ['Male', 'Female'];
        $genderStatsColors = [
            'Male' => '36b9cc',    // teal (match dashboard)
            'Female' => 'e74a3b',  // red (match dashboard)
        ];

        // Section titles and table headers to match (first cell)
        $sectionTitles = [
            'Telemedicine Consultation Report',
            'Date Range',
            'To',
            'Outgoing Consultations',
            'Incoming Consultations',
            'Consultations by Department (Outgoing)',
            'Consultations by Department (Incoming)',
            'Age Distribution (Outgoing)',
            'Age Distribution (Incoming)',
            'Diagnosis Statistics (Outgoing)',
            'Diagnosis Statistics (Incoming)',
            'Gender Distribution (Outgoing)',
            'Gender Distribution (Incoming)',
        ];
        // Diagnosis and Gender Distribution header colors
        $diagnosisHeaderColor = 'e74a3b'; // red (for diagnosis)
        $genderHeaderColor = '858796'; // gray (for gender)
        $diagnosisHeaderTitles = [
            'Diagnosis Statistics (Outgoing)',
            'Diagnosis Statistics (Incoming)'
        ];
        $genderHeaderTitles = [
            'Gender Distribution (Outgoing)',
            'Gender Distribution (Incoming)'
        ];
        $departmentTableHeader = ['Department', 'Total Consultations'];
        $statisticsTableHeader = [
            ['Total Consultations','Total Unique Patients', 'Average Consultation Duration', 'Total Seen', 'Total Follow Up', 'Total Accepted','Total Referred'],
            ['Total Consultations', 'Total Unique Patients', 'Average Consultation Duration', 'Total Follow Up', 'Total Accepted', 'Total Referred']
        ];
        $ageHeader = ['Below 18', '18-30', '31-45', '46-60', 'Above 60'];

        // Add chart/statistics card colors for main stats headers (from blade)
        $mainStatsHeaderColors = [
            // Outgoing
            'Total Consultations' => '4e73df', // blue
            'Departments' => '36b9cc', // teal
            'Patients' => '1cc88a', // green
            'Avg. Consultation Duration' => 'f6c23e', // yellow
            // Incoming (same colors)
        ];

        // Diagnosis statistics column colors
        $diagnosisStatsHeader = ['Hypertension', 'Diabetes', 'Respiratory', 'Cancer', 'Others'];
        $diagnosisStatsColors = [
            'Hypertension' => '4e73df', // blue
            'Diabetes' => '1cc88a',    // green
            'Respiratory' => '36b9cc', // teal
            'Cancer' => 'e74a3b',      // red
            'Others' => 'f6c23e',      // yellow
        ];
        // Gender Distribution header colors
        $genderStatsHeader = ['Male', 'Female'];
        $genderStatsColors = [
            'Male' => '36b9cc',   // teal (matches dashboard male color)
            'Female' => 'e83e8c', // pink (matches dashboard female color)
        ];

        foreach ($this->data as $rowIndex => $row) {
            // Check if the row is empty (all values are empty or a single empty string)
            $isEmpty = true;
            if (is_array($row)) {
                foreach ($row as $cell) {
                    if (trim((string)$cell) !== '') {
                        $isEmpty = false;
                        break;
                    }
                }
            } else {
                $isEmpty = trim((string)$row) === '';
            }
            if ($isEmpty) {
                continue;
            }

            // Calculate Excel row number (add 1 for 1-based index)
            $excelRow = $rowIndex + 1;
            $colCount = count($row);
            if ($colCount === 0) continue;
            if ($colCount > $maxCol) $maxCol = $colCount;
            $startCol = 'A';
            $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
            $cellRange = $startCol . $excelRow . ':' . $endCol . $excelRow;

            // Auto-merge if this is a "title" row (first cell has content, rest are empty)
            $isTitleRow = (
                $colCount > 1 &&
                trim((string)$row[0]) !== '' &&
                count(array_filter(array_slice($row, 1), function($cell) { return trim((string)$cell) !== ''; })) === 0
            );
            if ($isTitleRow) {
                $sheet->mergeCells($startCol . $excelRow . ':' . $endCol . $excelRow);
            }

            // Default: no fill
            $fillColor = null;
            $fontColor = $blackText;

            // Section header color
            // Only color if the row is a single-cell title row (merged), not for multi-cell rows like 'Date Range' and 'to' on the same row
            if ($isTitleRow && in_array(trim((string)$row[0]), $sectionTitles)) {
                // Do not color if this is the row with both 'Date Range' and 'to' (i.e., more than one non-empty cell)
                $nonEmptyCells = array_filter($row, function($cell) { return trim((string)$cell) !== ''; });
                if (count($nonEmptyCells) == 1) {
                    // Single-cell title row: color as section header
                    $fillColor = $sectionHeaderColor;
                    $fontColor = $blackText;
                } else {
                    // Multi-cell (e.g., 'Date Range' and 'to' on same row): no color
                    $fillColor = null;
                    $fontColor = $blackText;
                }
            }
            // Diagnosis Statistics section header
            if ($isTitleRow && in_array(trim((string)$row[0]), $diagnosisHeaderTitles)) {
                $fillColor = $diagnosisHeaderColor;
                $fontColor = $blackText;
            }
            // Gender Distribution section header
            if ($isTitleRow && in_array(trim((string)$row[0]), $genderHeaderTitles)) {
                $fillColor = $genderHeaderColor;
                $fontColor = $blackText;
            }
            // Department table header (always apply, even if previous if matched)
            if ($row === $departmentTableHeader) {
                $fillColor = $departmentHeaderColor;
                $fontColor = $blackText;
            }
            // Statistics table header
            if (in_array($row, $statisticsTableHeader)) {
                $fillColor = $statisticsHeaderColor;
                $fontColor = $blackText;
            }
            // Age distribution header
            if ($row === $ageHeader) {
                $fillColor = $ageHeaderColor;
                $fontColor = $blackText;
            }
            // Main statistics card headers (row with 4 columns, each cell is a stat header)
            elseif ($colCount === 4 && isset($row[0], $row[1], $row[2], $row[3])) {
                // Check if all cells match a known stat header
                $allMatch = true;
                foreach ($row as $cell) {
                    if (!isset($mainStatsHeaderColors[trim((string)$cell)])) {
                        $allMatch = false;
                        break;
                    }
                }
                if ($allMatch) {
                    // Color each cell individually
                    foreach ($row as $colIdx => $cell) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                        $cellRef = $colLetter . $excelRow;
                        $sheet->getStyle($cellRef)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => $mainStatsHeaderColors[trim((string)$cell)]],
                            ],
                            'font' => [
                                'color' => ['argb' => $blackText],
                            ],
                        ]);
                    }
                }
            }
            // Diagnosis statistics header row (Hypertension, Diabetes, ...)
            elseif ($row === $diagnosisStatsHeader) {
                foreach ($row as $colIdx => $cell) {
                    $cellText = trim((string)$cell);
                    if (isset($diagnosisStatsColors[$cellText])) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                        $cellRef = $colLetter . $excelRow;
                        $sheet->getStyle($cellRef)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                        $sheet->getStyle($cellRef)->getFill()->getStartColor()->setARGB($diagnosisStatsColors[$cellText]);
                    }
                }
            }
            // Gender distribution header row (Male, Female)
            elseif ($row === $genderStatsHeader) {
                foreach ($row as $colIdx => $cell) {
                    $cellText = trim((string)$cell);
                    if (isset($genderStatsColors[$cellText])) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                        $cellRef = $colLetter . $excelRow;
                        $sheet->getStyle($cellRef)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                        $sheet->getStyle($cellRef)->getFill()->getStartColor()->setARGB($genderStatsColors[$cellText]);
                    }
                }
            }
            // Gender Distribution header row (Male, Female)
            elseif ($row === $genderStatsHeader) {
                foreach ($row as $colIdx => $cell) {
                    $cellText = trim((string)$cell);
                    if (isset($genderStatsColors[$cellText])) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                        $cellRef = $colLetter . $excelRow;
                        $sheet->getStyle($cellRef)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                        $sheet->getStyle($cellRef)->getFill()->getStartColor()->setARGB($genderStatsColors[$cellText]);
                        $sheet->getStyle($cellRef)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($whiteText));
                    }
                }
            }

            // Apply borders, text wrap, center alignment, and fill if needed
            $sheet->getStyle($cellRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'alignment' => [
                    'wrapText' => true,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                ($fillColor ? 'fill' : null) => $fillColor ? [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => $fillColor],
                ] : null,
                'font' => [
                    'color' => ['argb' => $fontColor],
                ],
            ]);
        }
        // Auto-size all columns with data
        for ($col = 1; $col <= $maxCol; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        return [];
    }
}
