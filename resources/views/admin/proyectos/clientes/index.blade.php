@extends('layouts.admin-project', ['currentModule' => 'clientes'])

@section('title', 'Clientes | ' . $proyecto->nombre)
@section('module_label', 'Clientes')
@section('page_title', 'Clientes de ' . $proyecto->nombre)
@section('page_subtitle', 'Administra clientes, lotes vinculados, modalidad comercial y saldos del proyecto actual.')

@push('styles')
<style>
    .toolbar-form{display:flex;align-items:center;gap:12px;flex-wrap:wrap;flex:1;}
    .toolbar-select{min-width:180px;border:1.5px solid var(--border);background:#fff;border-radius:14px;padding:12px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);}
    .badge-modalidad,.badge-cliente{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
    .badge-modalidad::before,.badge-cliente::before{content:'';width:8px;height:8px;border-radius:50%;}
    .badge-modalidad.reservado{background:#fef3c7;color:#b45309;}
    .badge-modalidad.reservado::before{background:#d97706;}
    .badge-modalidad.financiamiento{background:#dbeafe;color:#1d4ed8;}
    .badge-modalidad.financiamiento::before{background:#2563eb;}
    .badge-modalidad.contado{background:#fee2e2;color:#b91c1c;}
    .badge-modalidad.contado::before{background:#dc2626;}
    .badge-cliente.activo{background:#dcfce7;color:#15803d;}
    .badge-cliente.activo::before{background:#16a34a;}
    .badge-cliente.desistido{background:#fef3c7;color:#b45309;}
    .badge-cliente.desistido::before{background:#d97706;}
    .badge-cliente.anulado{background:#fee2e2;color:#b91c1c;}
    .badge-cliente.anulado::before{background:#dc2626;}
    .client-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
    .action-icon{width:38px;height:38px;border-radius:12px;border:1.5px solid var(--border);background:#fff;color:#64748b;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;cursor:pointer;transition:.2s;}
    .action-icon:hover{transform:translateY(-1px);border-color:rgba(85,51,204,.28);background:#f8f9ff;color:var(--vt);}
    .action-icon.action-desist{background:#fffbeb;border-color:#fde68a;color:#d97706;}
    .action-icon.action-comments{background:#eff6ff;border-color:#bfdbfe;color:#2563eb;}
    .action-icon.action-delete:hover{background:#fff1f2;border-color:#fecdd3;color:#dc2626;}
    .action-icon:disabled{opacity:.45;cursor:not-allowed;transform:none;background:#f8fafc;color:#94a3b8;border-color:#e2e8f0;}
    .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.56);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;padding:20px;z-index:1100;}
    .modal-overlay.open{display:flex;}
    .modal-card{width:min(100%,560px);max-height:90vh;display:flex;flex-direction:column;background:#fff;border:1px solid var(--border);border-radius:24px;overflow:hidden;box-shadow:0 24px 70px rgba(15,23,42,.28);}
    .modal-card.modal-lg{width:min(100%,980px);}
    .modal-head{padding:22px 24px 16px;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;border-bottom:1px solid var(--border);}
    .modal-title{font-size:22px;font-weight:900;color:var(--text);line-height:1.15;}
    .modal-title span{color:var(--mg);}
    .modal-subtitle{margin-top:8px;font-size:12.5px;line-height:1.7;color:var(--gray);}
    .modal-close{width:40px;height:40px;border-radius:12px;border:1px solid var(--border);background:var(--bg);color:var(--gray);cursor:pointer;transition:.2s;display:inline-flex;align-items:center;justify-content:center;}
    .modal-close:hover{background:#fff;color:var(--text);border-color:rgba(85,51,204,.25);}
    .modal-body{padding:24px;overflow:auto;}
    .modal-foot{padding:18px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;}
    .helper-text{margin-top:8px;font-size:12px;color:var(--gray);line-height:1.55;}
    .helper-text strong{color:var(--text);}
    .readonly-field{background:#f8f9ff !important;color:var(--vt);font-weight:700;}
    .field-error{min-height:18px;}
    .modal-error{display:none;margin-bottom:16px;padding:12px 14px;border-radius:14px;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;font-size:12.5px;font-weight:600;line-height:1.55;}
    .modal-error.show{display:block;}
    .modal-note{padding:14px 16px;border-radius:16px;background:linear-gradient(135deg,rgba(238,0,187,.08),rgba(85,51,204,.08));font-size:12.5px;line-height:1.7;color:var(--gray);}
    .comments-list{display:flex;flex-direction:column;gap:12px;}
    .comment-card{border:1px solid var(--border);border-radius:16px;padding:14px 16px;background:#fff;}
    .comment-meta{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;}
    .comment-author{font-size:12px;font-weight:800;color:var(--text);}
    .comment-date{font-size:11.5px;color:var(--gray);}
    .comment-text{font-size:13px;line-height:1.7;color:var(--text);white-space:pre-wrap;}
    .comment-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:12px;}
    .mini-btn{border:1px solid var(--border);background:#fff;border-radius:10px;padding:7px 10px;font:700 11px 'Poppins',sans-serif;color:var(--gray);cursor:pointer;transition:.2s;}
    .mini-btn:hover{border-color:rgba(85,51,204,.28);color:var(--vt);}
    .mini-btn.danger:hover{border-color:#fecdd3;color:#dc2626;background:#fff1f2;}
    .comments-empty{padding:22px;border:1px dashed var(--border);border-radius:16px;text-align:center;color:var(--gray);font-size:13px;}
    .comments-form{margin-top:18px;padding-top:18px;border-top:1px solid var(--border);}
    .comments-form-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:10px;}
    .comments-form-title{font-size:13px;font-weight:800;color:var(--text);}
    .comments-form-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:12px;flex-wrap:wrap;}
    .toast{position:fixed;right:22px;bottom:22px;z-index:1300;display:flex;align-items:center;gap:10px;padding:14px 18px;border-radius:16px;background:#111827;color:#fff;box-shadow:0 16px 40px rgba(15,23,42,.24);opacity:0;pointer-events:none;transform:translateY(14px);transition:.22s;}
    .toast.show{opacity:1;transform:translateY(0);}
    .toast.error{background:#7f1d1d;}
    @media(max-width:860px){.client-actions{min-width:160px;}.modal-card.modal-lg{width:min(100%,720px);}}
    @media(max-width:640px){.modal-overlay{padding:12px;}.modal-head,.modal-body,.modal-foot{padding-left:16px;padding-right:16px;}.action-icon{width:36px;height:36px;}}
</style>
@endpush

@push('scripts')
<script>
    function resetComentarioForm() {
        currentCommentEditId = null;
        document.getElementById('comentarioTexto').value = '';
        document.getElementById('comentarioFormTitle').textContent = 'Nuevo comentario';
        document.querySelector('#comentarioForm [data-error="texto"]').textContent = '';
        document.getElementById('cancelComentarioEdit').style.display = 'none';
        document.getElementById('comentarioSubmit').innerHTML = '<i class="fas fa-paper-plane"></i> Guardar comentario';
    }

    function renderComentarios(comentarios) {
        const list = document.getElementById('comentariosList');
        comentariosCache = comentarios;

        if (!list) {
            return;
        }

        if (!comentarios.length) {
            list.innerHTML = '<div class="comments-empty">No hay comentarios registrados para este cliente.</div>';
            return;
        }

        list.innerHTML = comentarios.map((comentario) => `
            <article class="comment-card">
                <div class="comment-meta">
                    <div>
                        <div class="comment-author">${escapeHtml(comentario.autor || 'Administrador')}</div>
                        <div class="comment-date">${escapeHtml(comentario.fecha || '')}</div>
                    </div>
                </div>
                <div class="comment-text">${escapeHtml(comentario.texto || '')}</div>
                <div class="comment-actions">
                    <button type="button" class="mini-btn" onclick="startComentarioEdit(${comentario.id})">Editar</button>
                    <button type="button" class="mini-btn danger" onclick="deleteComentario(${comentario.id})">Eliminar</button>
                </div>
            </article>
        `).join('');
    }

    async function openComentariosModal(id) {
        const cliente = getCliente(id);

        if (!cliente) {
            return;
        }

        currentCommentsClientId = id;
        comentariosCache = [];
        resetComentarioForm();
        clearComentariosError();
        document.getElementById('comentariosClienteNombre').textContent = cliente.nombre_completo;
        document.getElementById('comentariosList').innerHTML = '<div class="comments-empty">Cargando comentarios...</div>';
        openModal('clienteComentariosModal');
        await reloadComentarios();
    }

    async function reloadComentarios() {
        const cliente = getCliente(currentCommentsClientId);

        if (!cliente) {
            return;
        }

        try {
            const response = await fetch(cliente.comentarios_url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'No se pudieron cargar los comentarios.');
            }

            renderComentarios(data);
        } catch (error) {
            setComentariosError(error.message || 'No se pudieron cargar los comentarios.');
            document.getElementById('comentariosList').innerHTML = '<div class="comments-empty">No se pudo cargar la lista.</div>';
        }
    }

    function startComentarioEdit(commentId) {
        const comentario = comentariosCache.find((item) => Number(item.id) === Number(commentId));

        if (!comentario) {
            return;
        }

        currentCommentEditId = commentId;
        document.getElementById('comentarioTexto').value = comentario.texto || '';
        document.getElementById('comentarioFormTitle').textContent = 'Editar comentario';
        document.getElementById('cancelComentarioEdit').style.display = 'inline-flex';
        document.getElementById('comentarioSubmit').innerHTML = '<i class="fas fa-save"></i> Guardar cambios';
        document.getElementById('comentarioTexto').focus();
    }

    async function deleteComentario(commentId) {
        const cliente = getCliente(currentCommentsClientId);

        if (!cliente || !window.confirm('Se eliminara este comentario.')) {
            return;
        }

        clearComentariosError();

        try {
            const response = await fetch(`${cliente.comentarios_url}/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
            });
            const data = await response.json();

            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'No se pudo eliminar el comentario.');
            }

            showToast('Comentario eliminado.');
            await reloadComentarios();

            if (Number(currentCommentEditId) === Number(commentId)) {
                resetComentarioForm();
            }
        } catch (error) {
            setComentariosError(error.message || 'No se pudo eliminar el comentario.');
        }
    }

    function openDeleteModal(id) {
        const cliente = getCliente(id);

        if (!cliente) {
            return;
        }

        currentDeleteClientId = id;
        document.getElementById('deleteClienteNombre').textContent = cliente.nombre_completo;
        openModal('clienteDeleteModal');
    }
</script>
@endpush

@push('scripts')
<script>
    window.addEventListener('load', () => {
    document.getElementById('clienteEditForm')?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const cliente = getCliente(currentEditClientId);
        const form = event.currentTarget;
        const submit = document.getElementById('clienteEditSubmit');

        if (!cliente || !form) {
            return;
        }

        clearFormState(form);
        const formData = new FormData(form);

        setButtonLoading(submit, true, '<i class="fas fa-save"></i> Guardar cambios', '<i class="fas fa-spinner fa-spin"></i> Guardando...');

        try {
            const response = await fetch(cliente.update_url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: formData,
            });
            const data = await response.json();

            if (!response.ok) {
                applyValidationErrors(form, data.errors || {});
                setFormError(form, data.message || 'No se pudo actualizar el cliente.');
                return;
            }

            showToast(data.mensaje || 'Cliente actualizado correctamente.');
            window.setTimeout(() => window.location.reload(), 280);
        } catch (error) {
            setFormError(form, 'No se pudo actualizar el cliente.');
        } finally {
            setButtonLoading(submit, false, '<i class="fas fa-save"></i> Guardar cambios', '<i class="fas fa-spinner fa-spin"></i> Guardando...');
        }
    });

    document.getElementById('clienteDesistForm')?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const cliente = getCliente(currentDesistClientId);
        const form = event.currentTarget;
        const submit = document.getElementById('clienteDesistSubmit');

        if (!cliente || !form) {
            return;
        }

        clearFormState(form);
        const payload = {
            monto_devolucion: form.monto_devolucion.value === '' ? null : form.monto_devolucion.value,
            motivo: form.motivo.value,
        };

        setButtonLoading(submit, true, '<i class="fas fa-user-xmark"></i> Confirmar desistimiento', '<i class="fas fa-spinner fa-spin"></i> Procesando...');

        try {
            const response = await fetch(cliente.desistido_url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify(payload),
            });
            const data = await response.json();

            if (!response.ok) {
                applyValidationErrors(form, data.errors || {});
                setFormError(form, data.message || 'No se pudo registrar el desistimiento.');
                return;
            }

            showToast(data.egreso_registrado ? 'Cliente desistido y egreso registrado.' : 'Cliente marcado como desistido.');
            window.setTimeout(() => window.location.reload(), 280);
        } catch (error) {
            setFormError(form, 'No se pudo registrar el desistimiento.');
        } finally {
            setButtonLoading(submit, false, '<i class="fas fa-user-xmark"></i> Confirmar desistimiento', '<i class="fas fa-spinner fa-spin"></i> Procesando...');
        }
    });

    document.getElementById('comentarioForm')?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const cliente = getCliente(currentCommentsClientId);
        const textarea = document.getElementById('comentarioTexto');
        const error = document.querySelector('#comentarioForm [data-error="texto"]');
        const submit = document.getElementById('comentarioSubmit');

        if (!cliente || !textarea) {
            return;
        }

        error.textContent = '';
        clearComentariosError();

        setButtonLoading(
            submit,
            true,
            currentCommentEditId ? '<i class="fas fa-save"></i> Guardar cambios' : '<i class="fas fa-paper-plane"></i> Guardar comentario',
            '<i class="fas fa-spinner fa-spin"></i> Guardando...'
        );

        try {
            const url = currentCommentEditId ? `${cliente.comentarios_url}/${currentCommentEditId}` : cliente.comentarios_store_url;
            const method = currentCommentEditId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify({ texto: textarea.value }),
            });
            const data = await response.json();

            if (!response.ok) {
                if (data.errors?.texto) {
                    error.textContent = data.errors.texto[0];
                }
                setComentariosError(data.message || 'No se pudo guardar el comentario.');
                return;
            }

            showToast(currentCommentEditId ? 'Comentario actualizado.' : 'Comentario registrado.');
            resetComentarioForm();
            await reloadComentarios();
        } catch (errorCatch) {
            setComentariosError('No se pudo guardar el comentario.');
        } finally {
            setButtonLoading(
                submit,
                false,
                currentCommentEditId ? '<i class="fas fa-save"></i> Guardar cambios' : '<i class="fas fa-paper-plane"></i> Guardar comentario',
                '<i class="fas fa-spinner fa-spin"></i> Guardando...'
            );
        }
    });

    document.getElementById('cancelComentarioEdit')?.addEventListener('click', () => {
        resetComentarioForm();
    });

    document.getElementById('clienteDeleteSubmit')?.addEventListener('click', async () => {
        const cliente = getCliente(currentDeleteClientId);
        const button = document.getElementById('clienteDeleteSubmit');

        if (!cliente) {
            return;
        }

        setButtonLoading(button, true, '<i class="fas fa-trash"></i> Eliminar cliente', '<i class="fas fa-spinner fa-spin"></i> Eliminando...');

        try {
            const response = await fetch(cliente.destroy_url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
            });
            const data = await response.json();

            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'No se pudo eliminar el cliente.');
            }

            showToast('Cliente eliminado correctamente.');
            window.setTimeout(() => window.location.reload(), 280);
        } catch (error) {
            showToast(error.message || 'No se pudo eliminar el cliente.', true);
        } finally {
            setButtonLoading(button, false, '<i class="fas fa-trash"></i> Eliminar cliente', '<i class="fas fa-spinner fa-spin"></i> Eliminando...');
        }
    });

    ['edit_lote_id', 'edit_modalidad', 'edit_cuota_inicial'].forEach((id) => {
        const element = document.getElementById(id);
        element?.addEventListener('change', syncEditFinancials);
        element?.addEventListener('input', syncEditFinancials);
    });

    document.querySelectorAll('.modal-overlay').forEach((overlay) => {
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                overlay.classList.remove('open');
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach((overlay) => overlay.classList.remove('open'));
        }
    });
    });
</script>
@endpush

@section('content')
@php
    $cards = [
        ['key' => 'Total', 'class' => 'is-total', 'icon' => 'fas fa-users', 'label' => 'Total'],
        ['key' => 'activo', 'class' => 'is-libre', 'icon' => 'fas fa-user-check', 'label' => 'Activos'],
        ['key' => 'desistido', 'class' => 'is-reservado', 'icon' => 'fas fa-user-clock', 'label' => 'Desistidos'],
        ['key' => 'anulado', 'class' => 'is-vendido', 'icon' => 'fas fa-user-xmark', 'label' => 'Anulados'],
    ];

    $clientesPayload = $clientes->getCollection()
        ->mapWithKeys(function ($cliente) use ($proyecto) {
            return [
                (string) $cliente->id => [
                    'id' => $cliente->id,
                    'nombre_completo' => $cliente->nombre_completo,
                    'nombres' => $cliente->nombres,
                    'apellidos' => $cliente->apellidos,
                    'dni' => $cliente->dni,
                    'telefono' => $cliente->telefono,
                    'email' => $cliente->email,
                    'direccion' => $cliente->direccion,
                    'fecha_registro' => optional($cliente->fecha_registro)->format('Y-m-d'),
                    'lote_id' => $cliente->lote_id,
                    'modalidad' => $cliente->modalidad,
                    'estado' => $cliente->estado,
                    'cuota_inicial' => $cliente->cuota_inicial !== null ? (float) $cliente->cuota_inicial : null,
                    'cuota_mensual' => $cliente->cuota_mensual !== null ? (float) $cliente->cuota_mensual : null,
                    'observaciones' => $cliente->observaciones,
                    'update_url' => route('admin.proyectos.clientes.update', [$proyecto, $cliente]),
                    'desistido_url' => route('admin.proyectos.clientes.desistido', [$proyecto, $cliente]),
                    'destroy_url' => route('admin.proyectos.clientes.destroy', [$proyecto, $cliente]),
                    'comentarios_url' => route('admin.proyectos.clientes.comentarios', [$proyecto, $cliente]),
                    'comentarios_store_url' => route('admin.proyectos.clientes.comentarios.add', [$proyecto, $cliente]),
                ],
            ];
        })
        ->all();

    $lotesPayload = $lotesCatalog->map(function ($lote) {
        $clienteActivo = $lote->clienteActivo;

        return [
            'id' => $lote->id,
            'label' => 'Manzana ' . $lote->manzana . ' - Lote ' . $lote->numero,
            'codigo' => $lote->codigo,
            'precio' => (float) $lote->precio_inicial,
            'cliente_activo_id' => $clienteActivo?->id,
            'cliente_activo_nombre' => $clienteActivo
                ? trim(($clienteActivo->nombres ?? '') . ' ' . ($clienteActivo->apellidos ?? ''))
                : null,
        ];
    })->values()->all();
@endphp

<section class="summary-grid" style="grid-template-columns:repeat(4,minmax(0,1fr));">
    @foreach($cards as $card)
    <article class="card summary-card {{ $card['class'] }}">
        <div class="summary-icon">
            <i class="{{ $card['icon'] }}"></i>
        </div>
        <div>
            <h3>{{ $resumen[$card['key']] ?? 0 }}</h3>
            <p>{{ $card['label'] }}</p>
        </div>
    </article>
    @endforeach
</section>

<section class="card content-card">
    <div class="section-head">
        <div class="section-title">Listado de <span>Clientes</span></div>
        <a href="{{ route('admin.proyectos.clientes.create', $proyecto) }}" class="btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo cliente
        </a>
    </div>

    <form method="GET" action="{{ route('admin.proyectos.clientes', $proyecto) }}" class="toolbar-form" style="margin-bottom:18px;">
        <div class="search-box" style="margin-left:0;">
            <i class="fas fa-search"></i>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por nombre, DNI o lote...">
        </div>

        <select name="modalidad" class="toolbar-select">
            <option value="">Todas las modalidades</option>
            @foreach($modalidades as $item)
            <option value="{{ $item }}" @selected($modalidad === $item)>{{ ucfirst($item) }}</option>
            @endforeach
        </select>

        <select name="estado" class="toolbar-select">
            <option value="">Todos los estados</option>
            @foreach($estados as $item)
            <option value="{{ $item }}" @selected($estado === $item)>{{ ucfirst($item) }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn-primary">
            <i class="fas fa-filter"></i> Filtrar
        </button>
        <a href="{{ route('admin.proyectos.clientes', $proyecto) }}" class="btn-secondary">Limpiar</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>DNI</th>
                    <th>Telefono</th>
                    <th>Lote</th>
                    <th>Modalidad</th>
                    <th>Estado</th>
                    <th>Precio</th>
                    <th>Saldo pendiente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                <tr id="cliente-row-{{ $cliente->id }}">
                    <td>
                        <div class="cell-strong">{{ $cliente->nombre_completo }}</div>
                        @if($cliente->email)
                        <div class="muted">{{ $cliente->email }}</div>
                        @endif
                    </td>
                    <td>{{ $cliente->dni }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td>
                        @if($cliente->lote)
                        <div class="cell-strong">Mz. {{ $cliente->lote->manzana }} - Lt. {{ $cliente->lote->numero }}</div>
                        @if($cliente->lote->codigo)
                        <div class="muted">{{ $cliente->lote->codigo }}</div>
                        @endif
                        @else
                        <span class="muted">Sin lote</span>
                        @endif
                    </td>
                    <td><span class="badge-modalidad {{ $cliente->modalidad }}">{{ ucfirst($cliente->modalidad) }}</span></td>
                    <td><span class="badge-cliente {{ $cliente->estado }}">{{ ucfirst($cliente->estado) }}</span></td>
                    <td class="cell-strong">S/. {{ number_format((float) $cliente->precio_lote, 2, '.', ',') }}</td>
                    <td>S/. {{ number_format((float) $cliente->saldo_pendiente, 2, '.', ',') }}</td>
                    <td>
                        <div class="client-actions">
                            <button type="button" class="action-icon" title="Editar cliente" onclick="openEditCliente({{ $cliente->id }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button
                                type="button"
                                class="action-icon action-desist"
                                title="Marcar como desistido"
                                onclick="openDesistidoModal({{ $cliente->id }})"
                                @disabled($cliente->estado !== 'activo')
                            >
                                <i class="fas fa-user-xmark"></i>
                            </button>
                            <a
                                href="{{ route('admin.proyectos.documentos.create', ['proyecto' => $proyecto, 'contexto' => 'cliente', 'cliente_id' => $cliente->id, 'lote_id' => $cliente->lote_id, 'tipo_documento' => 'anexo']) }}"
                                class="action-icon"
                                title="Subir documentos"
                            >
                                <i class="fas fa-file-circle-plus"></i>
                            </a>
                            <button type="button" class="action-icon action-comments" title="Ver comentarios" onclick="openComentariosModal({{ $cliente->id }})">
                                <i class="fas fa-comment-dots"></i>
                            </button>
                            <button type="button" class="action-icon action-delete" title="Eliminar cliente" onclick="openDeleteModal({{ $cliente->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <strong>No hay clientes registrados en este proyecto.</strong>
                            <div style="margin-top:6px;">Registra el primer cliente y vincula su operacion con un lote disponible.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clientes->hasPages())
    <div class="pagination">
        <div class="pagination-status">
            Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} clientes
        </div>
        <div class="pagination-links">
            <a href="{{ $clientes->previousPageUrl() ?: '#' }}" class="page-link {{ $clientes->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-arrow-left"></i> Anterior
            </a>
            <a href="{{ $clientes->hasMorePages() ? $clientes->nextPageUrl() : '#' }}" class="page-link {{ $clientes->hasMorePages() ? '' : 'disabled' }}">
                Siguiente <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</section>

<div class="modal-overlay" id="clienteEditModal">
    <div class="modal-card modal-lg">
        <div class="modal-head">
            <div>
                <div class="modal-title">Editar <span>Cliente</span></div>
                <div class="modal-subtitle" id="clienteEditSubtitle">Actualiza los datos del cliente y el lote asociado.</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('clienteEditModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="clienteEditForm">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="modal-body">
                <div class="modal-error" data-form-error></div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_nombres">Nombres <span class="req">*</span></label>
                        <input type="text" id="edit_nombres" name="nombres" maxlength="150" required>
                        <div class="error-text field-error" data-error="nombres"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_apellidos">Apellidos <span class="req">*</span></label>
                        <input type="text" id="edit_apellidos" name="apellidos" maxlength="150" required>
                        <div class="error-text field-error" data-error="apellidos"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_dni">DNI <span class="req">*</span></label>
                        <input type="text" id="edit_dni" name="dni" inputmode="numeric" maxlength="8" required>
                        <div class="error-text field-error" data-error="dni"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_telefono">Telefono <span class="req">*</span></label>
                        <input type="text" id="edit_telefono" name="telefono" maxlength="20" required>
                        <div class="error-text field-error" data-error="telefono"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" id="edit_email" name="email" maxlength="150">
                        <div class="error-text field-error" data-error="email"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_fecha_registro">Fecha de registro <span class="req">*</span></label>
                        <input type="date" id="edit_fecha_registro" name="fecha_registro" required>
                        <div class="error-text field-error" data-error="fecha_registro"></div>
                    </div>

                    <div class="form-group full">
                        <label for="edit_direccion">Direccion</label>
                        <input type="text" id="edit_direccion" name="direccion" maxlength="255">
                        <div class="error-text field-error" data-error="direccion"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_lote_id">Lote <span class="req">*</span></label>
                        <select id="edit_lote_id" name="lote_id" required></select>
                        <div class="helper-text">Puedes cambiar a un lote libre o conservar el lote actual del cliente.</div>
                        <div class="error-text field-error" data-error="lote_id"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_modalidad">Modalidad <span class="req">*</span></label>
                        <select id="edit_modalidad" name="modalidad" required>
                            @foreach($modalidades as $item)
                            <option value="{{ $item }}">{{ ucfirst($item) }}</option>
                            @endforeach
                        </select>
                        <div class="error-text field-error" data-error="modalidad"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_estado">Estado <span class="req">*</span></label>
                        <select id="edit_estado" name="estado" required>
                            @foreach($estados as $item)
                            <option value="{{ $item }}">{{ ucfirst($item) }}</option>
                            @endforeach
                        </select>
                        <div class="error-text field-error" data-error="estado"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_precio_lote_preview">Precio del lote</label>
                        <input type="text" id="edit_precio_lote_preview" class="readonly-field" readonly>
                    </div>

                    <div class="form-group">
                        <label for="edit_cuota_inicial">Cuota inicial</label>
                        <input type="number" id="edit_cuota_inicial" name="cuota_inicial" step="0.01" min="0">
                        <div class="error-text field-error" data-error="cuota_inicial"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_cuota_mensual">Cuota mensual</label>
                        <input type="number" id="edit_cuota_mensual" name="cuota_mensual" step="0.01" min="0">
                        <div class="helper-text">Solo aplica para clientes en financiamiento.</div>
                        <div class="error-text field-error" data-error="cuota_mensual"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_saldo_pendiente_preview">Saldo pendiente estimado</label>
                        <input type="text" id="edit_saldo_pendiente_preview" class="readonly-field" readonly>
                    </div>

                    <div class="form-group full">
                        <label for="edit_observaciones">Observaciones</label>
                        <textarea id="edit_observaciones" name="observaciones"></textarea>
                        <div class="error-text field-error" data-error="observaciones"></div>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-secondary" onclick="closeModal('clienteEditModal')">Cancelar</button>
                <button type="submit" class="btn-primary" id="clienteEditSubmit">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="clienteDesistModal">
    <div class="modal-card">
        <div class="modal-head">
            <div>
                <div class="modal-title">Marcar como <span>Desistido</span></div>
                <div class="modal-subtitle" id="clienteDesistSubtitle">Registra motivo y devolucion si corresponde.</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('clienteDesistModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="clienteDesistForm">
            <div class="modal-body">
                <div class="modal-error" data-form-error></div>

                <div class="modal-note">
                    El lote quedara libre nuevamente. Si registras un monto mayor a cero, el sistema generara un egreso automatico en caja general.
                </div>

                <div class="form-grid" style="margin-top:18px;">
                    <div class="form-group">
                        <label for="desist_monto_devolucion">Monto de devolucion</label>
                        <input type="number" id="desist_monto_devolucion" name="monto_devolucion" min="0" step="0.01" placeholder="0.00">
                        <div class="helper-text">Usa 0 si no corresponde devolucion.</div>
                        <div class="error-text field-error" data-error="monto_devolucion"></div>
                    </div>

                    <div class="form-group full">
                        <label for="desist_motivo">Motivo <span class="req">*</span></label>
                        <textarea id="desist_motivo" name="motivo" required></textarea>
                        <div class="error-text field-error" data-error="motivo"></div>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-secondary" onclick="closeModal('clienteDesistModal')">Cancelar</button>
                <button type="submit" class="btn-primary" id="clienteDesistSubmit">
                    <i class="fas fa-user-xmark"></i> Confirmar desistimiento
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="clienteComentariosModal">
    <div class="modal-card">
        <div class="modal-head">
            <div>
                <div class="modal-title">Comentarios de <span id="comentariosClienteNombre">cliente</span></div>
                <div class="modal-subtitle">Puedes ver, agregar, editar y eliminar comentarios desde este modal.</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('clienteComentariosModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-error" id="comentariosError"></div>
            <div id="comentariosList" class="comments-list"></div>

            <form id="comentarioForm" class="comments-form">
                <div class="comments-form-head">
                    <div class="comments-form-title" id="comentarioFormTitle">Nuevo comentario</div>
                    <button type="button" class="mini-btn" id="cancelComentarioEdit" style="display:none;">Cancelar edicion</button>
                </div>
                <textarea id="comentarioTexto" name="texto" rows="4" placeholder="Escribe un comentario..."></textarea>
                <div class="error-text field-error" data-error="texto"></div>
                <div class="comments-form-actions">
                    <button type="submit" class="btn-primary" id="comentarioSubmit">
                        <i class="fas fa-paper-plane"></i> Guardar comentario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="clienteDeleteModal">
    <div class="modal-card">
        <div class="modal-head">
            <div>
                <div class="modal-title">Eliminar <span>Cliente</span></div>
                <div class="modal-subtitle">Se eliminara el cliente y sus datos relacionados del proyecto.</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('clienteDeleteModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-note">
                Esta accion eliminara pagos, ingresos, comentarios, cronograma y documentos vinculados al cliente <strong id="deleteClienteNombre"></strong>.
            </div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn-secondary" onclick="closeModal('clienteDeleteModal')">Cancelar</button>
            <button type="button" class="btn-primary" id="clienteDeleteSubmit">
                <i class="fas fa-trash"></i> Eliminar cliente
            </button>
        </div>
    </div>
</div>

<div class="toast" id="clientesToast">
    <i class="fas fa-circle-check"></i>
    <span id="clientesToastText"></span>
</div>
@endsection

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const clientesData = @json($clientesPayload);
    const lotesData = @json($lotesPayload);

    let currentEditClientId = null;
    let currentDesistClientId = null;
    let currentDeleteClientId = null;
    let currentCommentsClientId = null;
    let currentCommentEditId = null;
    let comentariosCache = [];

    function getCliente(id) {
        return clientesData[String(id)] || null;
    }

    function formatMoney(value) {
        const amount = Number.parseFloat(value || 0);
        return Number.isFinite(amount) ? amount.toFixed(2) : '0.00';
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function openModal(id) {
        document.getElementById(id)?.classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id)?.classList.remove('open');

        if (id === 'clienteEditModal') {
            clearFormState(document.getElementById('clienteEditForm'));
        }

        if (id === 'clienteDesistModal') {
            clearFormState(document.getElementById('clienteDesistForm'));
        }

        if (id === 'clienteComentariosModal') {
            resetComentarioForm();
            clearComentariosError();
        }
    }

    function showToast(message, isError = false) {
        const toast = document.getElementById('clientesToast');
        const text = document.getElementById('clientesToastText');

        if (!toast || !text) {
            return;
        }

        text.textContent = message;
        toast.classList.toggle('error', isError);
        toast.classList.add('show');

        window.clearTimeout(showToast.timeoutId);
        showToast.timeoutId = window.setTimeout(() => toast.classList.remove('show'), 2800);
    }

    function clearFormState(form) {
        if (!form) {
            return;
        }

        form.querySelectorAll('[data-error]').forEach((element) => {
            element.textContent = '';
        });

        const formError = form.querySelector('[data-form-error]');
        if (formError) {
            formError.textContent = '';
            formError.classList.remove('show');
        }
    }

    function setFormError(form, message) {
        const formError = form?.querySelector('[data-form-error]');
        if (!formError) {
            return;
        }

        formError.textContent = message;
        formError.classList.add('show');
    }

    function applyValidationErrors(form, errors = {}) {
        Object.entries(errors).forEach(([field, messages]) => {
            const errorTarget = form.querySelector(`[data-error="${field}"]`);
            if (errorTarget) {
                errorTarget.textContent = Array.isArray(messages) ? messages[0] : messages;
            }
        });
    }

    function setButtonLoading(button, loading, htmlDefault, htmlLoading) {
        if (!button) {
            return;
        }

        button.disabled = loading;
        button.innerHTML = loading ? htmlLoading : htmlDefault;
    }

    function renderEditLotes(cliente) {
        const select = document.getElementById('edit_lote_id');
        if (!select || !cliente) {
            return;
        }

        select.innerHTML = '<option value="">Selecciona un lote</option>';

        lotesData.forEach((lote) => {
            const isCurrent = Number(lote.id) === Number(cliente.lote_id);
            const occupiedByOther = lote.cliente_activo_id && Number(lote.cliente_activo_id) !== Number(cliente.id);
            const option = document.createElement('option');
            option.value = lote.id;
            option.dataset.precio = formatMoney(lote.precio);

            let label = lote.label;
            if (lote.codigo) {
                label += ` (${lote.codigo})`;
            }
            if (isCurrent) {
                label += ' - actual';
            } else if (occupiedByOther && lote.cliente_activo_nombre) {
                label += ` - ocupado por ${lote.cliente_activo_nombre}`;
            }

            option.textContent = label;
            option.disabled = occupiedByOther;

            if (String(lote.id) === String(cliente.lote_id)) {
                option.selected = true;
            }

            select.appendChild(option);
        });
    }

    function syncEditFinancials() {
        const loteSelect = document.getElementById('edit_lote_id');
        const modalidad = document.getElementById('edit_modalidad');
        const cuotaInicial = document.getElementById('edit_cuota_inicial');
        const cuotaMensual = document.getElementById('edit_cuota_mensual');
        const precioPreview = document.getElementById('edit_precio_lote_preview');
        const saldoPreview = document.getElementById('edit_saldo_pendiente_preview');

        if (!loteSelect || !modalidad || !cuotaInicial || !cuotaMensual || !precioPreview || !saldoPreview) {
            return;
        }

        const selectedOption = loteSelect.options[loteSelect.selectedIndex];
        const precio = selectedOption ? Number.parseFloat(selectedOption.dataset.precio || 0) : 0;
        const modalidadActual = modalidad.value;

        precioPreview.value = formatMoney(precio);

        if (modalidadActual === 'contado') {
            cuotaInicial.value = formatMoney(precio);
            cuotaInicial.readOnly = true;
            cuotaMensual.value = '';
            cuotaMensual.readOnly = true;
            saldoPreview.value = '0.00';
            return;
        }

        cuotaInicial.readOnly = false;
        cuotaMensual.readOnly = modalidadActual !== 'financiamiento';

        if (modalidadActual !== 'financiamiento') {
            cuotaMensual.value = '';
        }

        const inicial = Number.parseFloat(cuotaInicial.value || 0);
        const saldo = Math.max(precio - (Number.isFinite(inicial) ? inicial : 0), 0);

        saldoPreview.value = formatMoney(saldo);
    }

    function openEditCliente(id) {
        const cliente = getCliente(id);
        const form = document.getElementById('clienteEditForm');

        if (!cliente || !form) {
            return;
        }

        currentEditClientId = id;
        clearFormState(form);
        form.dataset.action = cliente.update_url;

        document.getElementById('clienteEditSubtitle').textContent = `Editando a ${cliente.nombre_completo}.`;
        document.getElementById('edit_nombres').value = cliente.nombres || '';
        document.getElementById('edit_apellidos').value = cliente.apellidos || '';
        document.getElementById('edit_dni').value = cliente.dni || '';
        document.getElementById('edit_telefono').value = cliente.telefono || '';
        document.getElementById('edit_email').value = cliente.email || '';
        document.getElementById('edit_fecha_registro').value = cliente.fecha_registro || '';
        document.getElementById('edit_direccion').value = cliente.direccion || '';
        document.getElementById('edit_modalidad').value = cliente.modalidad || 'reservado';
        document.getElementById('edit_estado').value = cliente.estado || 'activo';
        document.getElementById('edit_cuota_inicial').value = cliente.cuota_inicial ?? '';
        document.getElementById('edit_cuota_mensual').value = cliente.cuota_mensual ?? '';
        document.getElementById('edit_observaciones').value = cliente.observaciones || '';

        renderEditLotes(cliente);
        syncEditFinancials();
        openModal('clienteEditModal');
    }

    function openDesistidoModal(id) {
        const cliente = getCliente(id);
        const form = document.getElementById('clienteDesistForm');

        if (!cliente || !form) {
            return;
        }

        currentDesistClientId = id;
        form.reset();
        clearFormState(form);
        document.getElementById('clienteDesistSubtitle').textContent = `Cliente: ${cliente.nombre_completo}.`;
        document.getElementById('desist_monto_devolucion').value = '0.00';
        openModal('clienteDesistModal');
    }

    function clearComentariosError() {
        const error = document.getElementById('comentariosError');
        if (!error) {
            return;
        }

        error.textContent = '';
        error.classList.remove('show');
    }

    function setComentariosError(message) {
        const error = document.getElementById('comentariosError');
        if (!error) {
            return;
        }

        error.textContent = message;
        error.classList.add('show');
    }
</script>
@endpush
