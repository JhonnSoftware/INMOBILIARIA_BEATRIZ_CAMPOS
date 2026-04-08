@php
    $estadoActual = old('estado', $lote->estado ?: 'Libre');
@endphp

<div class="form-grid">
    <div class="form-group">
        <label for="manzana">Manzana <span class="req">*</span></label>
        <input type="text" id="manzana" name="manzana" value="{{ old('manzana', $lote->manzana) }}" maxlength="20" required>
        @error('manzana')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="numero">Número de lote <span class="req">*</span></label>
        <input type="text" id="numero" name="numero" value="{{ old('numero', $lote->numero) }}" maxlength="20" required>
        @error('numero')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="codigo">Código</label>
        <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $lote->codigo) }}" maxlength="50">
        @error('codigo')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="estado">Estado <span class="req">*</span></label>
        <select id="estado" name="estado" required>
            @foreach($estados as $estado)
            <option value="{{ $estado }}" @selected($estadoActual === $estado)>{{ $estado }}</option>
            @endforeach
        </select>
        @error('estado')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="metraje">Metraje (m²) <span class="req">*</span></label>
        <input type="number" step="0.01" min="0" id="metraje" name="metraje" value="{{ old('metraje', $lote->metraje) }}" required>
        @error('metraje')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="precio_inicial">Precio inicial (S/) <span class="req">*</span></label>
        <input type="number" step="0.01" min="0" id="precio_inicial" name="precio_inicial" value="{{ old('precio_inicial', $lote->precio_inicial) }}" required>
        @error('precio_inicial')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion">{{ old('descripcion', $lote->descripcion) }}</textarea>
        @error('descripcion')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="observaciones">Observaciones</label>
        <textarea id="observaciones" name="observaciones">{{ old('observaciones', $lote->observaciones) }}</textarea>
        @error('observaciones')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="fecha_venta">Fecha de venta</label>
        <input type="date" id="fecha_venta" name="fecha_venta" value="{{ old('fecha_venta', optional($lote->fecha_venta)->format('Y-m-d')) }}">
        @error('fecha_venta')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a href="{{ route('admin.proyectos.lotes', $proyecto) }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">
        <i class="fas fa-save"></i> {{ $submitLabel }}
    </button>
</div>
