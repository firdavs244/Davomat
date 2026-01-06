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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DavomatExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithEvents
{
    protected $guruh;
    protected $sanaDan;
    protected $sanaGacha;
    protected $rowCount = 1; // Header uchun

    public function __construct(Guruh $guruh, Carbon $sanaDan, Carbon $sanaGacha)
    {
        $this->guruh = $guruh;
        $this->sanaDan = $sanaDan;
        $this->sanaGacha = $sanaGacha;
    }

    /**
     * Ma'lumotlarni to'plash
     */
    public function collection()
    {
        $talabalar = Talaba::where('guruh_id', $this->guruh->id)
            ->orderBy('fish')
            ->get();

        $data = collect();
        $currentDate = $this->sanaDan->copy();

        while ($currentDate->lte($this->sanaGacha)) {
            // Faqat ish kunlari (dushanba - shanba)
            if ($currentDate->dayOfWeek != Carbon::SUNDAY) {
                foreach ($talabalar as $talaba) {
                    $row = $this->getTalabaRow($talaba, $currentDate);
                    $data->push($row);
                    $this->rowCount++;
                }
            }
            $currentDate->addDay();
        }

        return $data;
    }

    /**
     * Sarlavhalar
     */
    public function headings(): array
    {
        return [
            '№',
            'Talaba FISH',
            'Guruh',
            'Sana',
            'Hafta kuni',
            '1-para',
            '2-para',
            '3-para',
            'Jami yo\'qlar',
            'Davomat %',
            'Izoh'
        ];
    }

    /**
     * Ustunlar kengligi
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // №
            'B' => 30,  // Talaba FISH
            'C' => 15,  // Guruh
            'D' => 12,  // Sana
            'E' => 12,  // Hafta kuni
            'F' => 10,  // 1-para
            'G' => 10,  // 2-para
            'H' => 10,  // 3-para
            'I' => 12,  // Jami yo'qlar
            'J' => 12,  // Davomat %
            'K' => 40,  // Izoh
        ];
    }

    /**
     * Sheet nomi
     */
    public function title(): string
    {
        return substr("Davomat {$this->guruh->nomi}", 0, 31);
    }

    /**
     * Stil berish
     */
    public function styles(Worksheet $sheet)
    {
        // Header stilini belgilash
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
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
        $sheet->getStyle('A1:K' . $this->rowCount)->applyFromArray([
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
                $sheet->getStyle('A2:K' . $this->rowCount)->applyFromArray([
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

                // Izohni chapga
                $sheet->getStyle('K2:K' . $this->rowCount)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ]
                ]);

                // Rang berish - yo'q bo'lgan qatorlar
                for ($row = 2; $row <= $this->rowCount; $row++) {
                    $jamiYoq = $sheet->getCell('I' . $row)->getValue();

                    if ($jamiYoq && is_numeric($jamiYoq) && $jamiYoq > 0) {
                        $sheet->getStyle('I' . $row)->applyFromArray([
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
                    $foiz = $sheet->getCell('J' . $row)->getValue();
                    if ($foiz && is_numeric(str_replace('%', '', $foiz))) {
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

                        $sheet->getStyle('J' . $row)->applyFromArray([
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
            }
        ];
    }

    /**
     * Talaba uchun qator ma'lumotlarini olish
     */
    private function getTalabaRow(Talaba $talaba, Carbon $sana): array
    {
        static $counter = 0;
        $counter++;

        // Talaba bu kunda kollej talabasi ekanligini tekshirish
        if (!$talaba->isKollejTalabasi($sana)) {
            $izoh = $this->getNullIzoh($talaba, $sana);
            return [
                $counter,
                $talaba->fish,
                $this->guruh->nomi,
                $sana->format('d.m.Y'),
                $this->getHaftaKuni($sana),
                '',
                '',
                '',
                '',
                '',
                $izoh,
            ];
        }

        // Davomat ma'lumotlarini olish
        $davomat = Davomat::where('talaba_id', $talaba->id)
            ->where('sana', $sana->toDateString())
            ->first();

        if (!$davomat) {
            return [
                $counter,
                $talaba->fish,
                $this->guruh->nomi,
                $sana->format('d.m.Y'),
                $this->getHaftaKuni($sana),
                '-',
                '-',
                '-',
                '-',
                '',
                'Davomat olinmagan',
            ];
        }

        $para1 = $this->getParaQiymati($davomat->para_1);
        $para2 = $this->getParaQiymati($davomat->para_2);
        $para3 = $this->getParaQiymati($davomat->para_3);

        $jamiYoq = $davomat->jami_yoq;

        // Davomat foizini hisoblash
        $borSoni = 0;
        $jamiParalar = 0;
        foreach (['para_1', 'para_2', 'para_3'] as $para) {
            if ($davomat->$para === 'bor') $borSoni++;
            if (in_array($davomat->$para, ['bor', 'yoq'])) $jamiParalar++;
        }
        $foiz = $jamiParalar > 0 ? round(($borSoni / $jamiParalar) * 100, 1) : 0;

        return [
            $counter,
            $talaba->fish,
            $this->guruh->nomi,
            $sana->format('d.m.Y'),
            $this->getHaftaKuni($sana),
            $para1,
            $para2,
            $para3,
            $jamiYoq > 0 ? $jamiYoq : '-',
            $foiz > 0 ? $foiz . '%' : '',
            $davomat->izoh ?? '-',
        ];
    }

    /**
     * Para qiymatini o'zbek tiliga tarjima qilish
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
     * Hafta kunini olish
     */
    private function getHaftaKuni(Carbon $sana): string
    {
        return match ($sana->dayOfWeek) {
            Carbon::MONDAY => 'Dushanba',
            Carbon::TUESDAY => 'Seshanba',
            Carbon::WEDNESDAY => 'Chorshanba',
            Carbon::THURSDAY => 'Payshanba',
            Carbon::FRIDAY => 'Juma',
            Carbon::SATURDAY => 'Shanba',
            default => 'Yakshanba',
        };
    }

    /**
     * Talaba kollej talabasi bo'lmagan kun uchun izoh
     */
    private function getNullIzoh(Talaba $talaba, Carbon $sana): string
    {
        if ($sana->lt($talaba->kirgan_sana)) {
            return "Talaba bu kunda hali kollej talabasi emas edi (kirgan sana: {$talaba->kirgan_sana->format('d.m.Y')})";
        }

        if ($talaba->ketgan_sana && $sana->gt($talaba->ketgan_sana)) {
            return "Talaba bu kunda kollej talabasi emas edi (ketgan sana: {$talaba->ketgan_sana->format('d.m.Y')})";
        }

        return "Talaba bu kunda kollej talabasi emas edi";
    }
}
