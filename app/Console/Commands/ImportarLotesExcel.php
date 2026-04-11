<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Proyecto;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportarLotesExcel extends Command
{
    protected $signature   = 'importar:lotes {archivo : Ruta completa al archivo Excel}';
    protected $description = 'Importa lotes y clientes desde la plantilla Excel de BC Inmobiliaria';

    public function handle(): int
    {
        $ruta = $this->argument('archivo');

        if (! file_exists($ruta)) {
            $this->error("Archivo no encontrado: {$ruta}");
            return self::FAILURE;
        }

        $this->info("Leyendo archivo: {$ruta}");

        try {
            $spreadsheet = IOFactory::load($ruta);
        } catch (\Exception $e) {
            $this->error("Error al leer el Excel: " . $e->getMessage());
            return self::FAILURE;
        }

        // ── IMPORTAR LOTES ───────────────────────────────────────────────
        $this->info('');
        $this->info('━━━ HOJA 1: LOTES ━━━');

        $lotesSheet = $spreadsheet->getSheetByName('Lotes');
        if (! $lotesSheet) {
            $this->error('No se encontró la hoja "Lotes".');
            return self::FAILURE;
        }

        $lotesCreados  = 0;
        $lotesOmitidos = 0;
        $erroresLotes  = [];
        $maxRow        = $lotesSheet->getHighestDataRow();

        for ($row = 5; $row <= $maxRow; $row++) {
            $proyecto = trim((string) $lotesSheet->getCell("A{$row}")->getValue());
            $manzana  = trim((string) $lotesSheet->getCell("B{$row}")->getValue());
            $numero   = trim((string) $lotesSheet->getCell("C{$row}")->getValue());

            if ($proyecto === '' && $manzana === '' && $numero === '') continue;

            if ($proyecto === '' || $manzana === '' || $numero === '') {
                $erroresLotes[] = "Fila {$row}: Proyecto, Manzana y N° Lote son obligatorios.";
                $lotesOmitidos++;
                continue;
            }

            $proyectoModel = Proyecto::whereRaw('LOWER(nombre) = ?', [strtolower($proyecto)])->first();
            if (! $proyectoModel) {
                $erroresLotes[] = "Fila {$row}: Proyecto '{$proyecto}' no existe en el sistema.";
                $lotesOmitidos++;
                continue;
            }

            $estado         = trim((string) $lotesSheet->getCell("G{$row}")->getValue());
            $estadosValidos = ['Libre', 'Reservado', 'Financiamiento', 'Vendido'];
            if (! in_array($estado, $estadosValidos)) {
                $erroresLotes[] = "Fila {$row}: Estado '{$estado}' inválido. Usa: " . implode(', ', $estadosValidos);
                $lotesOmitidos++;
                continue;
            }

            $existe = Lote::where('proyecto_id', $proyectoModel->id)
                ->where('manzana', $manzana)
                ->where('numero', $numero)
                ->exists();

            if ($existe) {
                $erroresLotes[] = "Fila {$row}: Lote {$manzana}-{$numero} ya existe en '{$proyecto}'. Omitido.";
                $lotesOmitidos++;
                continue;
            }

            $metraje       = (float) $lotesSheet->getCell("E{$row}")->getValue();
            $precioInicial = (float) $lotesSheet->getCell("F{$row}")->getValue();
            $codigo        = trim((string) $lotesSheet->getCell("D{$row}")->getValue());
            $fechaVenta    = trim((string) $lotesSheet->getCell("H{$row}")->getFormattedValue());
            $descripcion   = trim((string) $lotesSheet->getCell("I{$row}")->getValue());
            $observaciones = trim((string) $lotesSheet->getCell("J{$row}")->getValue());

            $fechaVentaParsed = null;
            if ($fechaVenta !== '') {
                try {
                    $fechaVentaParsed = Carbon::createFromFormat('d/m/Y', $fechaVenta)->toDateString();
                } catch (\Exception) {
                    $erroresLotes[] = "Fila {$row}: Fecha de venta '{$fechaVenta}' inválida (usa DD/MM/AAAA).";
                }
            }

            if ($codigo === '') {
                $codigo = strtoupper(substr($proyectoModel->nombre_corto ?: $proyectoModel->nombre, 0, 2))
                    . '-' . strtoupper($manzana)
                    . '-' . str_pad($numero, 2, '0', STR_PAD_LEFT);
            }

            try {
                Lote::create([
                    'proyecto_id'    => $proyectoModel->id,
                    'manzana'        => $manzana,
                    'numero'         => $numero,
                    'codigo'         => $codigo,
                    'metraje'        => $metraje ?: null,
                    'precio_inicial' => $precioInicial ?: null,
                    'estado'         => $estado,
                    'fecha_venta'    => $fechaVentaParsed,
                    'descripcion'    => $descripcion ?: null,
                    'observaciones'  => $observaciones ?: null,
                ]);
                $lotesCreados++;
                $this->line("  ✔ Fila {$row}: Lote {$manzana}-{$numero} creado en '{$proyecto}'");
            } catch (\Exception $e) {
                $erroresLotes[] = "Fila {$row}: Error — " . $e->getMessage();
                $lotesOmitidos++;
            }
        }

        // ── IMPORTAR CLIENTES ────────────────────────────────────────────
        $this->info('');
        $this->info('━━━ HOJA 2: CLIENTES ━━━');

        $clientesSheet    = $spreadsheet->getSheetByName('Clientes');
        $clientesCreados  = 0;
        $clientesOmitidos = 0;
        $erroresClientes  = [];

        if ($clientesSheet) {
            $maxRowC = $clientesSheet->getHighestDataRow();

            for ($row = 5; $row <= $maxRowC; $row++) {
                $proyecto  = trim((string) $clientesSheet->getCell("A{$row}")->getValue());
                $manzana   = trim((string) $clientesSheet->getCell("B{$row}")->getValue());
                $numero    = trim((string) $clientesSheet->getCell("C{$row}")->getValue());
                $nombres   = trim((string) $clientesSheet->getCell("D{$row}")->getValue());
                $apellidos = trim((string) $clientesSheet->getCell("E{$row}")->getValue());

                if ($proyecto === '' && $manzana === '' && $numero === '' && $nombres === '') continue;

                if ($proyecto === '' || $manzana === '' || $numero === '' || $nombres === '' || $apellidos === '') {
                    $erroresClientes[] = "Fila {$row}: Proyecto, Manzana, Lote, Nombres y Apellidos son obligatorios.";
                    $clientesOmitidos++;
                    continue;
                }

                $proyectoModel = Proyecto::whereRaw('LOWER(nombre) = ?', [strtolower($proyecto)])->first();
                if (! $proyectoModel) {
                    $erroresClientes[] = "Fila {$row}: Proyecto '{$proyecto}' no existe.";
                    $clientesOmitidos++;
                    continue;
                }

                $lote = Lote::where('proyecto_id', $proyectoModel->id)
                    ->where('manzana', $manzana)
                    ->where('numero', $numero)
                    ->first();

                if (! $lote) {
                    $erroresClientes[] = "Fila {$row}: Lote {$manzana}-{$numero} no existe en '{$proyecto}'.";
                    $clientesOmitidos++;
                    continue;
                }

                $modalidad = strtolower(trim((string) $clientesSheet->getCell("K{$row}")->getValue()));
                if (! in_array($modalidad, ['reservado', 'financiamiento', 'contado'])) {
                    $erroresClientes[] = "Fila {$row}: Modalidad '{$modalidad}' inválida.";
                    $clientesOmitidos++;
                    continue;
                }

                $dni         = trim((string) $clientesSheet->getCell("F{$row}")->getValue());
                $telefono    = trim((string) $clientesSheet->getCell("G{$row}")->getValue());
                $email       = trim((string) $clientesSheet->getCell("H{$row}")->getValue());
                $direccion   = trim((string) $clientesSheet->getCell("I{$row}")->getValue());
                $fechaReg    = trim((string) $clientesSheet->getCell("J{$row}")->getFormattedValue());
                $precioLote  = (float) $clientesSheet->getCell("L{$row}")->getValue();
                $cuotaIni    = (float) $clientesSheet->getCell("M{$row}")->getValue();
                $cuotaMen    = (float) $clientesSheet->getCell("N{$row}")->getValue();
                $nCuotas     = (int)   $clientesSheet->getCell("O{$row}")->getValue();
                $totalPagado = (float) $clientesSheet->getCell("P{$row}")->getValue();
                $saldoPend   = (float) $clientesSheet->getCell("Q{$row}")->getValue();
                $observ      = trim((string) $clientesSheet->getCell("R{$row}")->getValue());

                $fechaRegParsed = now()->toDateString();
                if ($fechaReg !== '') {
                    try {
                        $fechaRegParsed = Carbon::createFromFormat('d/m/Y', $fechaReg)->toDateString();
                    } catch (\Exception) {
                        $this->warn("  ⚠ Fila {$row}: Fecha registro inválida, se usará hoy.");
                    }
                }

                $estadoCobranza = match($modalidad) {
                    'reservado'      => 'reservado',
                    'financiamiento' => $totalPagado > 0 ? 'financiamiento' : 'sin_pagos',
                    'contado'        => $saldoPend <= 0 ? 'pagado' : 'financiamiento',
                    default          => 'sin_pagos',
                };

                try {
                    Cliente::create([
                        'proyecto_id'     => $proyectoModel->id,
                        'lote_id'         => $lote->id,
                        'nombres'         => $nombres,
                        'apellidos'       => $apellidos,
                        'dni'             => $dni ?: null,
                        'telefono'        => $telefono ?: null,
                        'email'           => $email ?: null,
                        'direccion'       => $direccion ?: null,
                        'fecha_registro'  => $fechaRegParsed,
                        'modalidad'       => $modalidad,
                        'estado'          => 'activo',
                        'estado_cobranza' => $estadoCobranza,
                        'precio_lote'     => $precioLote ?: null,
                        'cuota_inicial'   => $cuotaIni ?: null,
                        'cuota_mensual'   => $cuotaMen ?: null,
                        'numero_cuotas'   => $nCuotas ?: null,
                        'total_pagado'    => $totalPagado,
                        'saldo_pendiente' => $saldoPend,
                        'observaciones'   => $observ ?: null,
                    ]);

                    // Actualizar estado del lote según modalidad
                    $estadoLote = match($modalidad) {
                        'contado'        => 'Vendido',
                        'financiamiento' => 'Financiamiento',
                        'reservado'      => 'Reservado',
                        default          => $lote->estado,
                    };
                    $lote->update(['estado' => $estadoLote]);

                    $clientesCreados++;
                    $this->line("  ✔ Fila {$row}: '{$nombres} {$apellidos}' → Lote {$manzana}-{$numero}");
                } catch (\Exception $e) {
                    $erroresClientes[] = "Fila {$row}: Error — " . $e->getMessage();
                    $clientesOmitidos++;
                }
            }
        }

        // ── RESUMEN ──────────────────────────────────────────────────────
        $this->info('');
        $this->info('━━━━━━━━━━━━━━━━━━ RESUMEN ━━━━━━━━━━━━━━━━━━');
        $this->info("  Lotes creados    : {$lotesCreados}");
        $this->warn("  Lotes omitidos   : {$lotesOmitidos}");
        $this->info("  Clientes creados : {$clientesCreados}");
        $this->warn("  Clientes omitidos: {$clientesOmitidos}");

        if (! empty($erroresLotes)) {
            $this->info('');
            $this->error('Errores en Lotes:');
            foreach ($erroresLotes as $e) $this->line("  · {$e}");
        }

        if (! empty($erroresClientes)) {
            $this->info('');
            $this->error('Errores en Clientes:');
            foreach ($erroresClientes as $e) $this->line("  · {$e}");
        }

        $this->info('');
        $this->info('✔ Importación completada.');
        return self::SUCCESS;
    }
}
