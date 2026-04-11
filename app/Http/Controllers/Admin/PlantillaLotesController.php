<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlantillaLotesController extends Controller
{
    public function download(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Plantilla Importacion Lotes')
            ->setDescription('BC Inmobiliaria - Plantilla para carga masiva de lotes y clientes');

        // ── HOJA 1: LOTES ───────────────────────────────────────────────
        $lotesSheet = $spreadsheet->getActiveSheet();
        $lotesSheet->setTitle('Lotes');
        $this->buildLotesSheet($lotesSheet);

        // ── HOJA 2: CLIENTES ────────────────────────────────────────────
        $clientesSheet = new Worksheet($spreadsheet, 'Clientes');
        $spreadsheet->addSheet($clientesSheet);
        $this->buildClientesSheet($clientesSheet);

        // ── HOJA 3: INSTRUCCIONES ───────────────────────────────────────
        $instrSheet = new Worksheet($spreadsheet, 'Instrucciones');
        $spreadsheet->addSheet($instrSheet);
        $this->buildInstrSheet($instrSheet);

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'plantilla_lotes_bc_inmobiliaria.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function buildLotesSheet(Worksheet $sheet): void
    {
        // Título
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'BC INMOBILIARIA — PLANTILLA DE LOTES');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '5533CC']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Subtítulo
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'Completa un lote por fila. Los campos con * son obligatorios. No modifiques los encabezados.');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '5533CC']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EDE8FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Encabezados
        $headers = [
            'A' => ['PROYECTO *',           'Nombre exacto del proyecto (Ej: Mi Hogar, Carretera Central)'],
            'B' => ['MANZANA *',            'Letra o código de manzana (Ej: A, B, MZ-1)'],
            'C' => ['N° LOTE *',            'Número del lote (Ej: 1, 2, 10)'],
            'D' => ['CÓDIGO',               'Código único opcional (Ej: MH-A-01). Si se deja vacío se auto-genera.'],
            'E' => ['METRAJE (m²) *',       'Área del lote en metros cuadrados (Ej: 120.50)'],
            'F' => ['PRECIO INICIAL (S/) *','Precio de venta del lote en soles (Ej: 25000.00)'],
            'G' => ['ESTADO *',             'Libre / Reservado / Financiamiento / Vendido'],
            'H' => ['FECHA DE VENTA',       'Solo si está vendido. Formato: DD/MM/AAAA (Ej: 15/03/2024)'],
            'I' => ['DESCRIPCIÓN',          'Descripción opcional del lote (ubicación, frente, fondo, etc.)'],
            'J' => ['OBSERVACIONES',        'Notas internas opcionales'],
        ];

        $col = 'A';
        foreach ($headers as $col => [$header, $desc]) {
            // Fila de encabezado
            $sheet->setCellValue("{$col}3", $header);
            $isRequired = str_contains($header, '*');
            $sheet->getStyle("{$col}3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $isRequired ? 'FFFFFF' : '2D2D2D']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $isRequired ? 'EE00BB' : 'F3F0FF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
            $sheet->getRowDimension(3)->setRowHeight(32);

            // Fila de descripción
            $sheet->setCellValue("{$col}4", $desc);
            $sheet->getStyle("{$col}4")->applyFromArray([
                'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '6B7280']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FAFAFA']],
                'alignment' => ['wrapText' => true],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '5533CC']]],
            ]);
            $sheet->getRowDimension(4)->setRowHeight(40);
        }

        // Datos de ejemplo
        $ejemplos = [
            ['Mi Hogar', 'A', '1',  'MH-A-01', 120.00, 25000.00, 'Libre',          '',           'Lote esquinero, frente a calle principal', ''],
            ['Mi Hogar', 'A', '2',  'MH-A-02', 115.50, 23000.00, 'Vendido',        '15/03/2024', 'Lote regular',                             'Cliente referido'],
            ['Mi Hogar', 'B', '1',  'MH-B-01', 200.00, 42000.00, 'Financiamiento', '',           'Lote de esquina manzana B',                ''],
            ['Mi Hogar', 'B', '5',  '',         90.00, 18000.00, 'Reservado',      '',           '',                                         'Pendiente firma'],
        ];

        $row = 5;
        foreach ($ejemplos as $i => $ejemplo) {
            $bgColor = $i % 2 === 0 ? 'FFFFFF' : 'F8F6FF';
            $cols = ['A','B','C','D','E','F','G','H','I','J'];
            foreach ($cols as $j => $c) {
                $sheet->setCellValue("{$c}{$row}", $ejemplo[$j]);
                $sheet->getStyle("{$c}{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'font' => ['italic' => true, 'color' => ['rgb' => '94A3B8']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                ]);
            }
            // Formato número
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        // Filas vacías para llenar (5→30)
        for ($r = $row; $r <= 204; $r++) {
            $bgColor = $r % 2 === 0 ? 'FFFFFF' : 'FAFEFF';
            foreach (['A','B','C','D','E','F','G','H','I','J'] as $c) {
                $sheet->getStyle("{$c}{$r}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                ]);
            }
            $sheet->getStyle("E{$r}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("F{$r}")->getNumberFormat()->setFormatCode('#,##0.00');
        }

        // Validación dropdown ESTADO
        $validation = $sheet->getCell('G5')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(false);
        $validation->setFormula1('"Libre,Reservado,Financiamiento,Vendido"');
        $validation->setError('Usa: Libre, Reservado, Financiamiento o Vendido');
        $validation->setErrorTitle('Valor inválido');
        for ($r = 5; $r <= 204; $r++) {
            $sheet->getCell("G{$r}")->setDataValidation(clone $validation);
        }

        // Anchos de columna
        $widths = ['A' => 22, 'B' => 12, 'C' => 10, 'D' => 14, 'E' => 14, 'F' => 18, 'G' => 16, 'H' => 16, 'I' => 30, 'J' => 25];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Freeze encabezados
        $sheet->freezePane('A5');

        // Tab color
        $sheet->getTabColor()->setRGB('EE00BB');
    }

    private function buildClientesSheet(Worksheet $sheet): void
    {
        // Título
        $sheet->mergeCells('A1:R1');
        $sheet->setCellValue('A1', 'BC INMOBILIARIA — PLANTILLA DE CLIENTES');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        $sheet->mergeCells('A2:R2');
        $sheet->setCellValue('A2', 'Completa un cliente por fila. El MANZANA + N° LOTE debe coincidir exactamente con la hoja Lotes.');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '166534']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $headers = [
            'A' => ['PROYECTO *',           'Debe coincidir con la hoja Lotes'],
            'B' => ['MANZANA *',            'Igual que en la hoja Lotes (Ej: A)'],
            'C' => ['N° LOTE *',            'Igual que en la hoja Lotes (Ej: 1)'],
            'D' => ['NOMBRES *',            'Nombres del cliente (Ej: Juan Carlos)'],
            'E' => ['APELLIDOS *',          'Apellidos del cliente (Ej: García López)'],
            'F' => ['DNI *',                'DNI o documento de identidad (8 dígitos)'],
            'G' => ['TELÉFONO',             'Número de celular (Ej: 987654321)'],
            'H' => ['EMAIL',                'Correo electrónico'],
            'I' => ['DIRECCIÓN',            'Dirección de domicilio'],
            'J' => ['FECHA REGISTRO *',     'Fecha de registro. Formato: DD/MM/AAAA'],
            'K' => ['MODALIDAD *',          'reservado / financiamiento / contado'],
            'L' => ['PRECIO LOTE (S/) *',   'Precio pactado del lote'],
            'M' => ['CUOTA INICIAL (S/)',    'Monto de cuota inicial pagada'],
            'N' => ['CUOTA MENSUAL (S/)',    'Monto de cuota mensual (si aplica)'],
            'O' => ['N° CUOTAS',            'Número total de cuotas (si aplica)'],
            'P' => ['TOTAL PAGADO (S/)',     'Total pagado hasta la fecha'],
            'Q' => ['SALDO PENDIENTE (S/)', 'Saldo que queda por pagar'],
            'R' => ['OBSERVACIONES',        'Notas internas del cliente'],
        ];

        foreach ($headers as $col => [$header, $desc]) {
            $sheet->setCellValue("{$col}3", $header);
            $isRequired = str_contains($header, '*');
            $sheet->getStyle("{$col}3")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $isRequired ? 'FFFFFF' : '2D2D2D']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $isRequired ? '16A34A' : 'F0FDF4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
            $sheet->getRowDimension(3)->setRowHeight(32);

            $sheet->setCellValue("{$col}4", $desc);
            $sheet->getStyle("{$col}4")->applyFromArray([
                'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '6B7280']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FAFAFA']],
                'alignment' => ['wrapText' => true],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '16A34A']]],
            ]);
            $sheet->getRowDimension(4)->setRowHeight(40);
        }

        // Ejemplo
        $ejemplos = [
            ['Mi Hogar', 'A', '2', 'María', 'Quispe Ramos', '45678912', '987654321', 'maria@gmail.com', 'Jr. Lima 123', '15/03/2024', 'financiamiento', 23000.00, 5000.00, 600.00, 30, 8000.00, 15000.00, 'Cliente referido'],
            ['Mi Hogar', 'B', '1', 'Pedro', 'Huanca Torres', '76543210', '956123456', '',                'Av. Grau 456',  '20/01/2024', 'contado',        42000.00, 0.00,    0.00,   0,  42000.00, 0.00,     ''],
        ];

        $row = 5;
        foreach ($ejemplos as $i => $ejemplo) {
            $bgColor = $i % 2 === 0 ? 'FFFFFF' : 'F0FDF4';
            $cols = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'];
            foreach ($cols as $j => $c) {
                $sheet->setCellValue("{$c}{$row}", $ejemplo[$j]);
                $sheet->getStyle("{$c}{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'font' => ['italic' => true, 'color' => ['rgb' => '94A3B8']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                ]);
            }
            foreach (['L','M','N','P','Q'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            }
            $row++;
        }

        // Filas vacías
        for ($r = $row; $r <= 204; $r++) {
            $bgColor = $r % 2 === 0 ? 'FFFFFF' : 'FAFFFE';
            foreach (['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'] as $c) {
                $sheet->getStyle("{$c}{$r}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                ]);
            }
            foreach (['L','M','N','P','Q'] as $c) {
                $sheet->getStyle("{$c}{$r}")->getNumberFormat()->setFormatCode('#,##0.00');
            }
        }

        // Dropdown modalidad
        $valMod = $sheet->getCell('K5')->getDataValidation();
        $valMod->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $valMod->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $valMod->setAllowBlank(false);
        $valMod->setShowDropDown(false);
        $valMod->setFormula1('"reservado,financiamiento,contado"');
        $valMod->setError('Usa: reservado, financiamiento o contado');
        $valMod->setErrorTitle('Valor inválido');
        for ($r = 5; $r <= 204; $r++) {
            $sheet->getCell("K{$r}")->setDataValidation(clone $valMod);
        }

        $widths = ['A'=>20,'B'=>12,'C'=>10,'D'=>18,'E'=>20,'F'=>13,'G'=>14,'H'=>24,'I'=>26,'J'=>16,'K'=>16,'L'=>16,'M'=>16,'N'=>16,'O'=>12,'P'=>16,'Q'=>18,'R'=>24];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->freezePane('A5');
        $sheet->getTabColor()->setRGB('16A34A');
    }

    private function buildInstrSheet(Worksheet $sheet): void
    {
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'INSTRUCCIONES DE USO');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '5533CC']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        $instrucciones = [
            ['', '', '', ''],
            ['PASO 1', 'Hoja: Lotes', 'Ingresa todos los lotes del proyecto. Cada fila = 1 lote. Los campos con * son obligatorios.', ''],
            ['PASO 2', 'Hoja: Clientes', 'Ingresa los clientes que ya tienen lote asignado. Usa el mismo PROYECTO + MANZANA + N° LOTE.', ''],
            ['PASO 3', 'Enviar archivo', 'Guarda el archivo y entrégalo al administrador del sistema para la importación.', ''],
            ['', '', '', ''],
            ['REGLAS IMPORTANTES', '', '', ''],
            ['→', 'Proyecto', 'El nombre del proyecto debe coincidir EXACTAMENTE con el nombre en el sistema.', ''],
            ['→', 'Fechas', 'Siempre en formato DD/MM/AAAA. Ejemplo: 25/03/2024', ''],
            ['→', 'Montos', 'Sin símbolo S/. Solo el número. Ejemplo: 25000.50', ''],
            ['→', 'Estado Lote', 'Solo: Libre, Reservado, Financiamiento, Vendido', ''],
            ['→', 'Modalidad', 'Solo: reservado, financiamiento, contado (en minúsculas)', ''],
            ['→', 'Saldo Pendiente', 'Precio Lote - Total Pagado = Saldo Pendiente', ''],
            ['→', 'Filas de ejemplo', 'Las filas en gris claro son ejemplos. Puedes borrarlas.', ''],
            ['', '', '', ''],
            ['CAMPOS OPCIONALES', '', '', ''],
            ['→', 'Código de lote', 'Si lo dejas vacío, el sistema genera uno automáticamente.', ''],
            ['→', 'Teléfono / Email / Dirección', 'Del cliente — no son obligatorios pero se recomienda.', ''],
            ['→', 'Cuota inicial / Mensual / N° cuotas', 'Solo para modalidad financiamiento.', ''],
            ['', '', '', ''],
            ['CONTACTO', 'Si tienes dudas, comunícate con el administrador del sistema.', '', ''],
        ];

        $row = 2;
        foreach ($instrucciones as $instr) {
            $sheet->setCellValue("A{$row}", $instr[0]);
            $sheet->setCellValue("B{$row}", $instr[1]);
            $sheet->mergeCells("C{$row}:D{$row}");
            $sheet->setCellValue("C{$row}", $instr[2]);

            if (in_array($instr[0], ['PASO 1','PASO 2','PASO 3'])) {
                $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE00BB']],
                ]);
            } elseif (in_array($instr[0], ['REGLAS IMPORTANTES','CAMPOS OPCIONALES','CONTACTO'])) {
                $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '5533CC']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EDE8FF']],
                ]);
            }

            $sheet->getStyle("A{$row}:D{$row}")->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(55);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getTabColor()->setRGB('F59E0B');
    }
}
