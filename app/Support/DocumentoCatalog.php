<?php

namespace App\Support;

class DocumentoCatalog
{
    public const CONTEXTOS = [
        'proyecto' => 'Proyecto',
        'lote' => 'Lote',
        'cliente' => 'Cliente',
        'operacion' => 'Operacion',
    ];

    public const TIPOS = [
        'contrato' => 'Contrato',
        'voucher' => 'Voucher',
        'reserva' => 'Reserva',
        'financiamiento' => 'Financiamiento',
        'venta' => 'Venta',
        'plano' => 'Plano',
        'dni' => 'DNI',
        'ficha_cliente' => 'Ficha cliente',
        'anexo' => 'Anexo',
        'otro' => 'Otro',
    ];

    public const ESTADOS = [
        'activo' => 'Activo',
        'eliminado' => 'Eliminado',
    ];

    public const EXTENSIONES_PERMITIDAS = [
        'pdf',
        'doc',
        'docx',
        'jpg',
        'jpeg',
        'png',
        'xls',
        'xlsx',
        'txt',
        'svg',
        'dwg',
        'dxf',
    ];

    public static function directory(
        int $proyectoId,
        string $contexto,
        ?int $loteId = null,
        ?int $clienteId = null,
        ?int $pagoId = null
    ): string {
        $base = "documentos/proyectos/{$proyectoId}";

        return match ($contexto) {
            'lote' => $base . '/lotes/' . ($loteId ?: 'general'),
            'cliente' => $base . '/clientes/' . ($clienteId ?: 'general'),
            'operacion' => trim($base
                . '/operaciones'
                . ($clienteId ? "/clientes/{$clienteId}" : '')
                . ($loteId ? "/lotes/{$loteId}" : '')
                . ($pagoId ? "/pagos/{$pagoId}" : ''), '/'),
            default => $base . '/proyecto',
        };
    }

    public static function humanSize(?int $bytes): string
    {
        if (! $bytes || $bytes < 1) {
            return '0 KB';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $bytes;
        $power = 0;

        while ($size >= 1024 && $power < count($units) - 1) {
            $size /= 1024;
            $power++;
        }

        return number_format($size, $power === 0 ? 0 : 2, '.', ',') . ' ' . $units[$power];
    }
}
