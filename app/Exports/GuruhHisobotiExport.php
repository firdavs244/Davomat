<?php

namespace App\Exports;

use App\Models\Davomat;
use App\Models\Guruh;
use App\Models\Talaba;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GuruhHisobotiExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithEvents
{
    protected $guruh;
    protected $oy;
    protected $yil;
    protected $rowCount = 1;

    public function __construct(Guruh $guruh, int $oy, int $yil)
    {
        $this->guruh = $guruh;
        $this->oy = $oy;
        $this->yil = $yil;
    }

    /**
     * Ma'lumotlarni to'plash
     */
    public function collection()
    {
        $oyBoshi = Carbon::create($this->yil, $this->oy, 1)->startOfMonth();
        $oyOxiri = Carbon::create($this->yil, $this->oy, 1)->endOfMonth();

        $talabalar = Talaba::where('guruh_id', $this->guruh->id)
            ->orderBy('fish')
            ->get();

        $hisobot = collect();

        foreach ($talabalar as $talaba) {
            $davomatlar = Davomat::where('talaba_id', $talaba->id)
                ->whereBetween('sana', [$oyBoshi, $oyOxiri])
                ->get();

            $borSoni = 0;
            $yoqSoni = 0;
            $jamiParalar = 0;

            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3'] as $para) {
                    if ($d->$para === 'bor') {
                        $borSoni++;
                        $jamiParalar++;
                    } elseif ($d->$para === 'yoq') {
                        $yoqSoni++;
                        $jamiParalar++;
                    }
                }
            }

            $foiz = $jamiParalar > 0 ? round(($borSoni / $jamiParalar) * 100, 1) : 0;

            $this->rowCount++;
            $hisobot->push([
                $this->rowCount - 1,
                $talaba->fish,
                $borSoni,
                $yoqSoni,
                $jamiParalar,
                $foiz . '%',
                $this->getBaho($foiz)
            ]);
        }

        // Foiz bo'yicha saralash (yuqoridan pastga)
        $sorted = $hisobot->sortByDesc(function ($item) {
            return floatval(str_replace('%', '', $item[5]));
        })->values();

        return $sorted;
    }

    /**
     * Sarlavhalar
     */
    public function headings(): array
    {
        $oyNomi = $this->getOyNomi($this->oy);
        return [
            '№',
            'Talaba FISH',
            'Bor soni',
            'Yo\'q soni',
            'Jami paralar',
            'Davomat %',
            'Baho'
        ];
    }

    /**
     * Ustunlar kengligi
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // №
            'B' => 35,  // Talaba FISH
            'C' => 12,  // Bor soni
            'D' => 12,  // Yo'q soni
            'E' => 15,  // Jami paralar
            'F' => 12,  // Davomat %
            'G' => 12,  // Baho
        ];
    }

    /**
     * Sheet nomi
     */
    public function title(): string
    {
        $oyNomi = $this->getOyNomi($this->oy);
        return substr("{$oyNomi} - {$this->guruh->nomi}", 0, 31);
    }

    /**
     * Stil berish
     */
    public function styles(Worksheet $sheet)
    {
        // Header stilini belgilash
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Barcha ma'lumotlar uchun border
        $sheet->getStyle('A1:G' . $this->rowCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D0D0D0']
                ]
            ]
        ]);

        return [];
    }

    /**
     * Qo'shimcha hodisalar
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Matnlarni markazga joylashtirish
                $sheet->getStyle('A2:G' . $this->rowCount)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // Talaba nomlarini chapga
                $sheet->getStyle('B2:B' . $this->rowCount)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ]
                ]);

                // Yo'q soniga rang berish
                for ($row = 2; $row <= $this->rowCount; $row++) {
                    $yoqSoni = $sheet->getCell('D' . $row)->getValue();

                    if ($yoqSoni && is_numeric($yoqSoni) && $yoqSoni > 0) {
                        $sheet->getStyle('D' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFC7CE']
                            ],
                            'font' => [
                                'color' => ['rgb' => '9C0006'],
                                'bold' => true
                            ]
                        ]);
                    }

                    // Davomat foizi rang berish
                    $foiz = $sheet->getCell('F' . $row)->getValue();
                    if ($foiz) {
                        $foizValue = floatval(str_replace('%', '', $foiz));
                        if ($foizValue >= 90) {
                            $color = 'C6EFCE'; // Yashil
                            $fontColor = '006100';
                        } elseif ($foizValue >= 70) {
                            $color = 'FFEB9C'; // Sariq
                            $fontColor = '9C5700';
                        } else {
                            $color = 'FFC7CE'; // Qizil
                            $fontColor = '9C0006';
                        }

                        $sheet->getStyle('F' . $row . ':G' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $color]
                            ],
                            'font' => [
                                'color' => ['rgb' => $fontColor],
                                'bold' => true
                            ]
                        ]);
                    }
                }

                // Header qatorini qotirish
                $sheet->freezePane('A2');

                // Jami qatorini qo'shish
                $lastRow = $this->rowCount + 2;
                $sheet->setCellValue('B' . $lastRow, 'JAMI:');
                $sheet->setCellValue('C' . $lastRow, '=SUM(C2:C' . $this->rowCount . ')');
                $sheet->setCellValue('D' . $lastRow, '=SUM(D2:D' . $this->rowCount . ')');
                $sheet->setCellValue('E' . $lastRow, '=SUM(E2:E' . $this->rowCount . ')');
                $sheet->setCellValue('F' . $lastRow, '=AVERAGE(F2:F' . $this->rowCount . ')&"%"');

                $sheet->getStyle('B' . $lastRow . ':G' . $lastRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
            }
        ];
    }

    /**
     * Foiz bo'yicha baho berish
     */
    private function getBaho(float $foiz): string
    {
        if ($foiz >= 90) return 'A\'lo';
        if ($foiz >= 80) return 'Yaxshi';
        if ($foiz >= 70) return 'Qoniqarli';
        if ($foiz >= 60) return 'O\'rtacha';
        return 'Yomon';
    }

    /**
     * Oy nomini olish
     */
    private function getOyNomi(int $oy): string
    {
        return match($oy) {
            1 => 'Yanvar',
            2 => 'Fevral',
            3 => 'Mart',
            4 => 'Aprel',
            5 => 'May',
            6 => 'Iyun',
            7 => 'Iyul',
            8 => 'Avgust',
            9 => 'Sentabr',
            10 => 'Oktabr',
            11 => 'Noyabr',
            12 => 'Dekabr',
            default => 'Noma\'lum',
        };
    }
}
