<?php

namespace App\Exports;

use App\Models\Davomat;
use App\Models\Guruh;
use App\Models\Talaba;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GuruhHisobotiExport implements
    FromArray,
    WithStyles,
    WithTitle,
    WithEvents
{
    protected $guruh;
    protected $oy;
    protected $yil;
    protected $sanalar = [];
    protected $talabalar;
    protected $totalColumns = 0;
    protected $totalRows = 0;

    public function __construct(Guruh $guruh, int $oy, int $yil)
    {
        $this->guruh = $guruh;
        $this->oy = $oy;
        $this->yil = $yil;
    }

    /**
     * Ma'lumotlarni array sifatida qaytarish
     */
    public function array(): array
    {
        // Oy boshidan oxirigacha sanalarni to'plash (faqat ish kunlari)
        $oyBoshi = Carbon::create($this->yil, $this->oy, 1)->startOfMonth();
        $oyOxiri = Carbon::create($this->yil, $this->oy, 1)->endOfMonth();

        $currentDate = $oyBoshi->copy();
        while ($currentDate->lte($oyOxiri)) {
            if ($currentDate->dayOfWeek != Carbon::SUNDAY) {
                $this->sanalar[] = $currentDate->copy();
            }
            $currentDate->addDay();
        }

        // Talabalarni olish
        $this->talabalar = Talaba::where('guruh_id', $this->guruh->id)
            ->orderBy('fish')
            ->get();

        $data = [];

        // 1-qator: Sana sarlavhalari
        $row1 = ['№', 'Talaba FISH'];
        foreach ($this->sanalar as $sana) {
            $row1[] = $sana->format('d') . ' (' . $this->getHaftaKuni($sana) . ')';
            $row1[] = ''; // 2-para uchun bo'sh
            $row1[] = ''; // 3-para uchun bo'sh
            $row1[] = ''; // 4-para uchun bo'sh
        }
        $row1[] = 'Jami'; // Jami ustuni
        $row1[] = '';
        $row1[] = '';
        $data[] = $row1;

        // 2-qator: Para sarlavhalari
        $row2 = ['', ''];
        foreach ($this->sanalar as $sana) {
            $row2[] = '1';
            $row2[] = '2';
            $row2[] = '3';
            $row2[] = '4';
        }
        $row2[] = 'Bor';
        $row2[] = "Yo'q";
        $row2[] = '%';
        $data[] = $row2;

        // Talabalar ma'lumotlari
        $counter = 0;
        foreach ($this->talabalar as $talaba) {
            $counter++;
            $row = [$counter, $talaba->fish];

            $jamiParalar = 0;
            $borSoni = 0;
            $yoqSoni = 0;

            foreach ($this->sanalar as $sana) {
                // Talaba bu kunda kollej talabasi ekanligini tekshirish
                if (!$talaba->isKollejTalabasi($sana)) {
                    $row[] = '-';
                    $row[] = '-';
                    $row[] = '-';
                    $row[] = '-';
                    continue;
                }

                // Davomat ma'lumotlarini olish
                $davomat = Davomat::where('talaba_id', $talaba->id)
                    ->where('sana', $sana->toDateString())
                    ->first();

                if (!$davomat) {
                    $row[] = '-';
                    $row[] = '-';
                    $row[] = '-';
                    $row[] = '-';
                    continue;
                }

                // Para qiymatlarini qo'shish
                $row[] = $this->getParaQiymati($davomat->para_1);
                $row[] = $this->getParaQiymati($davomat->para_2);
                $row[] = $this->getParaQiymati($davomat->para_3);
                $row[] = $this->getParaQiymati($davomat->para_4);

                // Jami hisoblash
                foreach (['para_1', 'para_2', 'para_3', 'para_4'] as $para) {
                    if ($davomat->$para === 'bor') {
                        $borSoni++;
                        $jamiParalar++;
                    } elseif ($davomat->$para === 'yoq') {
                        $yoqSoni++;
                        $jamiParalar++;
                    }
                }
            }

            // Jami ustunlari
            $foiz = $jamiParalar > 0 ? round(($borSoni / $jamiParalar) * 100, 1) : 0;
            $row[] = $borSoni;
            $row[] = $yoqSoni;
            $row[] = $foiz . '%';

            $data[] = $row;
        }

        $this->totalColumns = count($row1);
        $this->totalRows = count($data);

        return $data;
    }

    /**
     * Sheet nomi
     */
    public function title(): string
    {
        $oyNomi = $this->getOyNomi($this->oy);
        return substr("{$oyNomi} {$this->yil} - {$this->guruh->nomi}", 0, 31);
    }

    /**
     * Stil berish
     */
    public function styles(Worksheet $sheet)
    {
        $lastCol = $this->getColumnLetter($this->totalColumns);

        // 1-qator (Sanalar) stilini belgilash
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
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
        ]);

        // 2-qator (Paralar) stilini belgilash
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '43A047']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Sana ustunlarini birlashtirish
        $sheet->mergeCells('A1:A2'); // №
        $sheet->mergeCells('B1:B2'); // Talaba FISH

        $colIndex = 3; // C ustunidan boshlaymiz
        foreach ($this->sanalar as $sana) {
            $startCol = $this->getColumnLetter($colIndex);
            $endCol = $this->getColumnLetter($colIndex + 3);
            $sheet->mergeCells($startCol . '1:' . $endCol . '1');
            $colIndex += 4;
        }

        // Jami ustunlarini birlashtirish
        $jamiStartCol = $this->getColumnLetter($colIndex);
        $jamiEndCol = $this->getColumnLetter($colIndex + 2);
        $sheet->mergeCells($jamiStartCol . '1:' . $jamiEndCol . '1');

        // Barcha ma'lumotlar uchun border
        $sheet->getStyle('A1:' . $lastCol . $this->totalRows)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Ustun kengliklarini belgilash
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(28);

        for ($i = 3; $i <= $this->totalColumns; $i++) {
            $sheet->getColumnDimension($this->getColumnLetter($i))->setWidth(4);
        }

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
                $lastCol = $this->getColumnLetter($this->totalColumns);

                // Ma'lumotlarni markazga joylashtirish
                $sheet->getStyle('A3:' . $lastCol . $this->totalRows)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // Talaba nomlarini chapga
                $sheet->getStyle('B3:B' . $this->totalRows)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ]
                ]);

                // Para qiymatlariga rang berish
                for ($row = 3; $row <= $this->totalRows; $row++) {
                    for ($col = 3; $col <= $this->totalColumns - 3; $col++) {
                        $cellValue = $sheet->getCell($this->getColumnLetter($col) . $row)->getValue();

                        if ($cellValue === '✓') {
                            $sheet->getStyle($this->getColumnLetter($col) . $row)->applyFromArray([
                                'font' => ['color' => ['rgb' => '006100'], 'bold' => true],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'C6EFCE']
                                ]
                            ]);
                        } elseif ($cellValue === '✗') {
                            $sheet->getStyle($this->getColumnLetter($col) . $row)->applyFromArray([
                                'font' => ['color' => ['rgb' => '9C0006'], 'bold' => true],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'FFC7CE']
                                ]
                            ]);
                        }
                    }

                    // Foiz ustuniga rang berish
                    $foizCol = $this->getColumnLetter($this->totalColumns);
                    $foizValue = floatval(str_replace('%', '', $sheet->getCell($foizCol . $row)->getValue()));

                    if ($foizValue >= 90) {
                        $color = 'C6EFCE';
                        $fontColor = '006100';
                    } elseif ($foizValue >= 70) {
                        $color = 'FFEB9C';
                        $fontColor = '9C5700';
                    } else {
                        $color = 'FFC7CE';
                        $fontColor = '9C0006';
                    }

                    $sheet->getStyle($foizCol . $row)->applyFromArray([
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

                // Header qatorlarini qotirish
                $sheet->freezePane('C3');

                // Qator balandligi
                $sheet->getRowDimension(1)->setRowHeight(22);
                $sheet->getRowDimension(2)->setRowHeight(18);
            }
        ];
    }

    /**
     * Para qiymatini belgi sifatida qaytarish
     */
    private function getParaQiymati(?string $value): string
    {
        return match ($value) {
            'bor' => '✓',
            'yoq' => '✗',
            default => '-',
        };
    }

    /**
     * Hafta kunini olish (qisqa)
     */
    private function getHaftaKuni(Carbon $sana): string
    {
        return match ($sana->dayOfWeek) {
            Carbon::MONDAY => 'Du',
            Carbon::TUESDAY => 'Se',
            Carbon::WEDNESDAY => 'Ch',
            Carbon::THURSDAY => 'Pa',
            Carbon::FRIDAY => 'Ju',
            Carbon::SATURDAY => 'Sh',
            default => 'Ya',
        };
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

    /**
     * Ustun raqamini harfga aylantirish
     */
    private function getColumnLetter(int $columnNumber): string
    {
        $letter = '';
        while ($columnNumber > 0) {
            $mod = ($columnNumber - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $columnNumber = (int)(($columnNumber - $mod) / 26);
        }
        return $letter;
    }
}
