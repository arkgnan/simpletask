<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TasksExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithEvents,
    ShouldAutoSize,
    WithStrictNullComparison,
    WithTitle
{
    use Exportable;

    protected Builder $query;
    protected int $rowCount = 0;
    protected float $totalTimeTracked = 0;

    /**
     * @param Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $tasks = $this->query->get();
        $this->rowCount = $tasks->count();

        return $tasks;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Task List";
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            "Title",
            "Assignee",
            "Due Date",
            "Time Tracked",
            "Status",
            "Priority",
        ];
    }

    /**
     * @param Task $task
     * @return array
     */
    public function map($task): array
    {
        return [
            $task->title,
            $task->assignee?->name ?? "Unassigned",
            $task->due_date->format("Y-m-d"),
            $task->status->value,
            $task->priority->value,
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Style the first row (headers)
            1 => [
                "font" => ["bold" => true],
                "fill" => [
                    "fillType" => Fill::FILL_SOLID,
                    "startColor" => ["argb" => "FFD3D3D3"],
                ],
                "borders" => [
                    "outline" => [
                        "borderStyle" => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastDataRow = $this->rowCount + 1; // +1 for header row
                $summaryRow = $lastDataRow + 2; // Add a blank row before summary

                // Set Summary Row
                $event->sheet->setCellValue("A" . $summaryRow, "SUMMARY");
                $event->sheet->setCellValue(
                    "B" . $summaryRow,
                    "Total Tasks: " . $this->rowCount,
                );
                $event->sheet->setCellValue(
                    "D" . $summaryRow,
                    "Total Time Tracked: " . $this->totalTimeTracked,
                );

                // Apply Style to Summary Row
                $event->sheet
                    ->getStyle("A" . $summaryRow . ":F" . $summaryRow)
                    ->applyFromArray([
                        "font" => ["bold" => true],
                        "fill" => [
                            "fillType" => Fill::FILL_SOLID,
                            "startColor" => ["argb" => "FFD3D3D3"],
                        ],
                        "borders" => [
                            "outline" => [
                                "borderStyle" => Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                // Add borders to data cells
                $event->sheet->getStyle("A1:F" . $lastDataRow)->applyFromArray([
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Conditional formatting
                for ($row = 2; $row <= $lastDataRow; $row++) {
                    // Color cells based on priority
                    $priority = $event->sheet->getCell("F" . $row)->getValue();
                    if ($priority === "high") {
                        $event->sheet->getStyle("F" . $row)->applyFromArray([
                            "fill" => [
                                "fillType" => Fill::FILL_SOLID,
                                "startColor" => ["argb" => "FFFF9999"], // Light red
                            ],
                        ]);
                    } elseif ($priority === "medium") {
                        $event->sheet->getStyle("F" . $row)->applyFromArray([
                            "fill" => [
                                "fillType" => Fill::FILL_SOLID,
                                "startColor" => ["argb" => "FFFFD699"], // Light orange
                            ],
                        ]);
                    }

                    // Color cells based on status
                    $status = $event->sheet->getCell("E" . $row)->getValue();
                    if ($status === "completed") {
                        $event->sheet->getStyle("E" . $row)->applyFromArray([
                            "fill" => [
                                "fillType" => Fill::FILL_SOLID,
                                "startColor" => ["argb" => "FFD6FFD6"], // Light green
                            ],
                        ]);
                    } elseif ($status === "pending") {
                        $event->sheet->getStyle("E" . $row)->applyFromArray([
                            "fill" => [
                                "fillType" => Fill::FILL_SOLID,
                                "startColor" => ["argb" => "FFFFD6D6"], // Light red
                            ],
                        ]);
                    }
                }
            },
        ];
    }
}
