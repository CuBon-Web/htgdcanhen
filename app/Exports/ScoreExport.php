<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\models\dethi\DethiQuestion;

class ScoreExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $scores;
    protected $dethi;
    protected $allQuestions;
    protected $questionsByType;

    public function __construct($scores, $dethi)
    {
        $this->scores = $scores;
        $this->dethi = $dethi;
        
        // Get all questions for this exam
        $this->allQuestions = DethiQuestion::with(['part', 'answers' => function($query) {
                $query->orderBy('label');
            }])
            ->whereIn('dethi_part_id', $dethi->parts->pluck('id'))
            ->orderBy('dethi_part_id')
            ->orderBy('id')
            ->get();

        // Group questions by type
        $this->questionsByType = $this->allQuestions->groupBy('question_type');
    }

    public function collection()
    {
        return $this->scores;
    }

    public function headings(): array
    {
        $baseHeadings = [
            'STT',
            'Họ và tên học sinh',
            'Email',
            'Số điện thoại',
            'Điểm số',
            'Điểm tối đa',
        ];

        // Add general statistics (bỏ thống kê theo loại câu hỏi)
        $baseHeadings[] = 'Tổng câu hỏi';
        $baseHeadings[] = 'Tổng đúng';
        $baseHeadings[] = 'Tổng sai';
        $baseHeadings[] = 'Tổng trống';
        $baseHeadings[] = 'Thời gian làm bài';
        $baseHeadings[] = 'Trạng thái';

        // Add question detail columns
        foreach ($this->allQuestions as $question) {
            $partName = $question->part->part ?? 'Phần';
            $typeLabel = $this->getQuestionTypeLabel($question->question_type);
            
            // Add part name to header: Câu X (TN - Phần Y)
            $baseHeadings[] = "Câu {$question->question_no} ({$typeLabel} - {$partName})";
        }

        $baseHeadings[] = 'Ghi chú';

        return $baseHeadings;
    }

    public function map($score): array
    {
        static $counter = 0;
        $counter++;

        // Get student answers
        $studentAnswers = $score->answers->keyBy('question_id');

        // Overall statistics only (bỏ thống kê theo loại)
        $totalCorrect = 0;
        $totalWrong = 0;
        $totalBlank = 0;

        // Count overall
        foreach ($this->allQuestions as $question) {
            $studentAnswer = $studentAnswers->get($question->id);

            if (!$studentAnswer) {
                $totalBlank++;
            } else {
                if ($studentAnswer->is_correct == 1) {
                    $totalCorrect++;
                } else {
                    $totalWrong++;
                }
            }
        }

        // Format time
        $timeFormatted = $this->formatTime($score->actual_time);
        
        // Base row data
        $rowData = [
            $counter,
            $score->student->name ?? 'N/A',
            $score->student->email ?? 'N/A',
            $score->student->phone ?? 'N/A',
            $score->total_score ?? 0,
            $score->max_score ?? 10,
        ];

        // Add general statistics (bỏ thống kê theo loại câu hỏi)
        $totalQuestions = $this->allQuestions->count();
        $rowData[] = $totalQuestions;
        $rowData[] = $totalCorrect;
        $rowData[] = $totalWrong;
        $rowData[] = $totalBlank;
        $rowData[] = $timeFormatted;
        $rowData[] = $this->getStatusText($score->status);

        // Add question results
        foreach ($this->allQuestions as $question) {
            $studentAnswer = $studentAnswers->get($question->id);
            
            if (!$studentAnswer) {
                $rowData[] = 'Bỏ trống';
            } else {
                // Special handling for essay/short_answer questions
                if (in_array($question->question_type, ['essay', 'short_answer'])) {
                    if ($studentAnswer->graded_by && $studentAnswer->graded_at) {
                        // Đã chấm → Hiển thị điểm
                        $rowData[] = number_format($studentAnswer->score ?? 0, 1) . ' điểm';
                    } else {
                        // Chưa chấm
                        $rowData[] = 'Chưa chấm';
                    }
                }
                // Special handling for true_false_grouped questions
                elseif ($question->question_type === 'true_false_grouped' && $studentAnswer->answer_choice) {
                    $rowData[] = $this->formatTrueFalseDetail($question, $studentAnswer);
                } else {
                    // Multiple choice, fill in blank, etc.
                    if ($studentAnswer->is_correct == 1) {
                        $rowData[] = 'Đúng';
                    } else {
                        $rowData[] = 'Sai';
                    }
                }
            }
        }

        // Add note
        $rowData[] = $this->getNote($score);

        return $rowData;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $this->getColumnNameFromNumber($this->getTotalColumns());
        $lastRow = $sheet->getHighestRow();

        // Style cho header
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(45);

        // Style cho dữ liệu
        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);

            // Style cho cột tên (left align)
            $sheet->getStyle('B2:B' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ]
            ]);

            // Style cho cột điểm số
            $sheet->getStyle('E2:F' . $lastRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ]
            ]);

            // Style cho cột tổng quan (4 columns: Total Q, Correct, Wrong, Blank)
            // Bắt đầu từ cột G (column 7)
            $currentColNum = 7;
            $generalStatsStart = $this->getColumnNameFromNumber($currentColNum);
            $generalStatsEnd = $this->getColumnNameFromNumber($currentColNum + 3);
            $sheet->getStyle($generalStatsStart . '2:' . $generalStatsEnd . $lastRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF2CC']
                ]
            ]);
            $currentColNum += 4; // Now at column 11 (K)

            // Skip Time and Status columns (2 columns)
            $currentColNum += 2;

            // Style for question columns - color code them
            for ($row = 2; $row <= $lastRow; $row++) {
                $questionColNum = $currentColNum;
                
                foreach ($this->allQuestions as $question) {
                    $col = $this->getColumnNameFromNumber($questionColNum);
                    $cellValue = $sheet->getCell($col . $row)->getValue();
                    
                    if ($cellValue === 'Đúng') {
                        $sheet->getStyle($col . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'C6EFCE'] // Light green
                            ],
                            'font' => ['color' => ['rgb' => '006100'], 'bold' => true]
                        ]);
                    } elseif ($cellValue === 'Sai') {
                        $sheet->getStyle($col . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFC7CE'] // Light red
                            ],
                            'font' => ['color' => ['rgb' => '9C0006'], 'bold' => true]
                        ]);
                    } elseif ($cellValue === 'Bỏ trống') {
                        $sheet->getStyle($col . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFEB9C'] // Light yellow
                            ],
                            'font' => ['color' => ['rgb' => '9C6500']]
                        ]);
                    } elseif ($cellValue === 'Chưa chấm') {
                        // Essay/Short answer - Not graded yet
                        $sheet->getStyle($col . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFF4E6'] // Light orange
                            ],
                            'font' => ['color' => ['rgb' => 'FF6B00'], 'italic' => true]
                        ]);
                    } elseif (strpos($cellValue, 'điểm') !== false) {
                        // Essay/Short answer - Graded with score
                        $sheet->getStyle($col . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E8F5E9'] // Light green (different shade)
                            ],
                            'font' => ['color' => ['rgb' => '2E7D32'], 'bold' => true]
                        ]);
                    } elseif (strpos($cellValue, '-Đ') !== false || strpos($cellValue, '-S') !== false) {
                        // True/False detail format: "a-Đ, b-S, c-Đ"
                        // Apply mixed color for detailed answers
                        $sheet->getStyle($col . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E0F2F1'] // Light teal
                            ],
                            'font' => ['size' => 9]
                        ]);
                    }
                    
                    $questionColNum++;
                }
            }
        }

        return [];
    }

    public function columnWidths(): array
    {
        $widths = [];
        $colNum = 1;
        
        // Base columns
        $baseWidths = [6, 25, 25, 15, 10, 10]; // A-F
        foreach ($baseWidths as $width) {
            $widths[$this->getColumnNameFromNumber($colNum)] = $width;
            $colNum++;
        }

        // General statistics columns (4 columns) - bỏ thống kê theo loại
        for ($i = 0; $i < 4; $i++) {
            $widths[$this->getColumnNameFromNumber($colNum)] = 11;
            $colNum++;
        }

        // Time and Status columns
        $widths[$this->getColumnNameFromNumber($colNum)] = 15; // Time
        $colNum++;
        $widths[$this->getColumnNameFromNumber($colNum)] = 15; // Status
        $colNum++;

        // Question columns
        foreach ($this->allQuestions as $question) {
            $widths[$this->getColumnNameFromNumber($colNum)] = 12;
            $colNum++;
        }

        // Note column
        $widths[$this->getColumnNameFromNumber($colNum)] = 30;

        return $widths;
    }

    public function title(): string
    {
        return 'Kết quả: ' . substr($this->dethi->title ?? 'Đề thi', 0, 28);
    }

    /**
     * Convert column number to Excel column name
     * 1 => A, 2 => B, ..., 27 => AA, 28 => AB, etc.
     */
    private function getColumnNameFromNumber($num)
    {
        return Coordinate::stringFromColumnIndex($num);
    }

    /**
     * Get total number of columns
     */
    private function getTotalColumns()
    {
        $baseColumns = 6; // A-F
        $generalStatsColumns = 4; // General statistics (bỏ thống kê theo loại)
        $timeStatusColumns = 2; // Time and Status
        $questionColumns = count($this->allQuestions);
        $noteColumn = 1;
        
        return $baseColumns + $generalStatsColumns + $timeStatusColumns + $questionColumns + $noteColumn;
    }

    private function getQuestionTypeLabel($type)
    {
        $labels = [
            'multiple_choice' => 'TN',
            'true_false_grouped' => 'Đ/S',
            'fill_in_blank' => 'ĐT',
            'short_answer' => 'TL'
        ];
        return $labels[$type] ?? 'KH';
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Chưa hoàn thành';
            case 1:
                return 'Chưa chấm tự luận';
            case 2:
                return 'Đã hoàn thành';
            default:
                return 'Không xác định';
        }
    }

    private function formatTime($seconds)
    {
        if (!$seconds || $seconds <= 0) return 'N/A';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d giờ %d phút %d giây', $hours, $minutes, $secs);
        } elseif ($minutes > 0) {
            return sprintf('%d phút %d giây', $minutes, $secs);
        } else {
            return sprintf('%d giây', $secs);
        }
    }

    private function getNote($score)
    {
        $notes = [];
        
        $percentage = $score->max_score > 0 ? ($score->total_score / $score->max_score) * 10 : 0;
        
        if ($percentage >= 8) {
            $notes[] = 'Xuất sắc';
        } elseif ($percentage >= 6.5) {
            $notes[] = 'Khá';
        } elseif ($percentage >= 5) {
            $notes[] = 'Trung bình';
        } else {
            $notes[] = 'Cần cải thiện';
        }
        
        return implode(', ', $notes);
    }

    /**
     * Format chi tiết câu trả lời đúng/sai theo từng câu con
     * @param DethiQuestion $question
     * @param ExamAnswer $studentAnswer
     * @return string - Format: "a-Đ, b-S, c-Đ, d-Đ"
     */
    private function formatTrueFalseDetail($question, $studentAnswer)
    {
        try {
            // Parse student's choices: {"5":"0","6":"1","7":"0","8":"0"}
            // 0 = Sai, 1 = Đúng, 2 = Không chọn
            $studentChoices = json_decode($studentAnswer->answer_choice, true);
            
            if (!is_array($studentChoices)) {
                return $studentAnswer->is_correct == 1 ? 'Đúng' : 'Sai';
            }

            // Get sub-questions with correct answers
            $subQuestions = $question->answers()
                ->orderBy('label')
                ->get();

            $details = [];
            
            foreach ($subQuestions as $subQuestion) {
                $label = strtolower($subQuestion->label); // a, b, c, d
                $correctAnswer = $subQuestion->is_correct; // 0 or 1
                $studentChoice = $studentChoices[$subQuestion->id] ?? 2; // Student's choice
                
                // Check if student's answer is correct
                if ($studentChoice == 2) {
                    // Không chọn
                    $details[] = $label . '-Trống';
                } elseif ($studentChoice == $correctAnswer) {
                    // Đúng
                    $details[] = $label . '-Đ';
                } else {
                    // Sai
                    $details[] = $label . '-S';
                }
            }

            return implode(', ', $details);

        } catch (\Exception $e) {
            \Log::error('Error formatting true/false detail: ' . $e->getMessage());
            return $studentAnswer->is_correct == 1 ? 'Đúng' : 'Sai';
        }
    }
}
