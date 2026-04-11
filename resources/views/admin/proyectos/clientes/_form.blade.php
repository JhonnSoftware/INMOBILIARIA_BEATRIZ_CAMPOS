@php
    $selectedLoteId = (string) old('lote_id', $cliente->lote_id);
    $puedeGuardar = $lotes->isNotEmpty();
@endphp

@push('styles')
<style>
    .helper-text{margin-top:8px;font-size:12px;color:var(--gray);line-height:1.5;}
    .helper-text strong{color:var(--text);}
    .empty-note{margin-bottom:18px;padding:14px 16px;border-radius:14px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:13px;font-weight:600;}
</style>
@endpush

@if(! $puedeGuardar)
<div class="empty-note">
    No hay lotes libres disponibles en este proyecto. Libera o actualiza un lote antes de registrar un cliente nuevo.
</div>
@endif

<div class="form-grid">
    <div class="form-group">
        <label for="nombres">Nombres <span class="req">*</span></label>
        <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $cliente->nombres) }}" maxlength="150" required>
        @error('nombres')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="apellidos">Apellidos <span class="req">*</span></label>
        <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $cliente->apellidos) }}" maxlength="150" required>
        @error('apellidos')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="dni">DNI <span class="req">*</span></label>
        <input type="text" id="dni" name="dni" value="{{ old('dni', $cliente->dni) }}" inputmode="numeric" maxlength="8" required>
        @error('dni')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="telefono">Telefono <span class="req">*</span></label>
        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" maxlength="20" required>
        @error('telefono')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $cliente->email) }}" maxlength="150">
        @error('email')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="fecha_registro">Fecha de registro <span class="req">*</span></label>
        <input type="date" id="fecha_registro" name="fecha_registro" value="{{ old('fecha_registro', optional($cliente->fecha_registro)->format('Y-m-d') ?: now()->toDateString()) }}" required>
        @error('fecha_registro')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="direccion">Direccion</label>
        <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $cliente->direccion) }}" maxlength="255">
        @error('direccion')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="asesor_id">Asesor <span class="req">*</span></label>
        <select id="asesor_id" name="asesor_id" required>
            <option value="">Selecciona un asesor</option>
            @foreach($asesores as $asesor)
            <option value="{{ $asesor->id }}" @selected((string) old('asesor_id', $cliente->asesor_id) === (string) $asesor->id)>
                {{ $asesor->nombre_completo }}
            </option>
            @endforeach
        </select>
        <div class="helper-text">Asesor que gestionó el contacto con el cliente.</div>
        @error('asesor_id')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="lote_id">Lote <span class="req">*</span></label>
        <select id="lote_id" name="lote_id" required @disabled(! $puedeGuardar)>
            <option value="">Selecciona un lote</option>
            @foreach($lotes as $lote)
            @php
                $isCurrentLote = $cliente->lote_id === $lote->id && $lote->estado !== 'Libre';
            @endphp
            <option
                value="{{ $lote->id }}"
                data-precio="{{ number_format((float) $lote->precio_inicial, 2, '.', '') }}"
                data-estado="{{ $lote->estado }}"
                @selected($selectedLoteId === (string) $lote->id)
            >
                Manzana {{ $lote->manzana }} - Lote {{ $lote->numero }}
                @if($lote->codigo)
                    ({{ $lote->codigo }})
                @endif
                @if($isCurrentLote)
                    - actual
                @endif
            </option>
            @endforeach
        </select>
        <div class="helper-text">Solo se muestran lotes <strong>Libres</strong> del proyecto actual{{ $cliente->exists ? ' y el lote actual del cliente.' : '.' }}</div>
        @error('lote_id')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="modalidad">Modalidad <span class="req">*</span></label>
        <select id="modalidad" name="modalidad" required>
            @foreach($modalidades as $item)
            <option value="{{ $item }}" @selected(old('modalidad', $cliente->modalidad ?: 'reservado') === $item)>{{ ucfirst($item) }}</option>
            @endforeach
        </select>
        @error('modalidad')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="monto_reserva">Monto de reserva</label>
        <input type="number" id="monto_reserva" name="monto_reserva" value="{{ old('monto_reserva', $cliente->cuota_inicial) }}" step="0.01" min="0" placeholder="0.00">
        <div class="helper-text">Opcional. Adelanto inicial que el cliente paga al momento del registro.</div>
        @error('monto_reserva')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="form-actions">
    <a href="{{ route('admin.proyectos.clientes', $proyecto) }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary" @disabled(! $puedeGuardar)>
        <i class="fas fa-save"></i> {{ $submitLabel }}
    </button>
</div>

