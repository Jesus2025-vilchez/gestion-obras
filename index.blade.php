<x-app-layout>
    @push('styles')
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
    @endpush
    <div class="py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-boxes me-2"></i>Activos
                        </h5>
                    </div>
                    <div class="d-flex gap-2">

                        <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Nuevo Activo
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if(session("success"))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session("success") }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session("error"))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session("error") }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="table-responsive">
                    <table id="activosTable" class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3">ID</th>
                                <th class="py-3">Obra</th>  
                                <th class="py-3">Tipo</th>
                                <th class="py-3">Descripción</th>
                                <th class="py-3">Estado</th>
                                <th class="py-3">Fecha Registro</th>
                                <th class="py-3">Responsable</th>
                                <th class="py-3 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activos as $activo)
                            <tr>
                                <td>{{ $activo->id }}</td> 
                                <td>{{ $activo->obra->nombre ?? 'N/A' }}</td>  
                                <td>{{ $activo->tipo_activo }}</td>
                                <td>{{ $activo->descripcion }}</td>
                                <td>
                                    @php
                                        $estadoClases = [
                                            'disponible' => 'bg-success',
                                            'en_uso' => 'bg-primary',
                                            'mantenimiento' => 'bg-warning',
                                            'inactivo' => 'bg-danger'
                                        ];
                                        $estadoTexto = [
                                            'disponible' => 'Disponible',
                                            'en_uso' => 'En Uso',
                                            'mantenimiento' => 'Mantenimiento',
                                            'inactivo' => 'Inactivo'
                                        ];
                                    @endphp
                                    <span class="badge {{ $estadoClases[$activo->estado] }}">
                                        {{ $estadoTexto[$activo->estado] }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($activo->fecha_registro)->format('d/m/Y') }}</td>
                                <td>{{ $activo->user->name }}</td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('activos.mantenimientos.index', $activo) }}" 
                                           class="btn btn-warning btn-action" 
                                           data-bs-toggle="tooltip" 
                                           title="Ver mantenimientos">
                                            <i class="fas fa-tools"></i>
                                        </a>
                                        <button onclick="openEditModal({{ $activo->id }})" 
                                                class="btn btn-primary btn-action" 
                                                data-bs-toggle="tooltip" 
                                                title="Editar activo">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteActivo({{ $activo->id }})" 
                                                class="btn btn-danger btn-action" 
                                                data-bs-toggle="tooltip" 
                                                title="Eliminar activo">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createActivoModal" tabindex="-1" aria-labelledby="createActivoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2 bg-light">
                    <h6 class="modal-title" id="createActivoModalLabel">
                        <i class="fas fa-plus me-2"></i>Nuevo Activo
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createActivoForm" action="{{ route('activos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-3">
                        <div class="row g-3">
                        <div class="col-md-12">
                                    <label class="form-label small mb-1">Obra (Opcional)</label>
                                    <select class="form-select form-select-sm" name="obra_id" id="edit_obra_id">
                                        <option value="">Sin asignar</option>
                                        @foreach($obras as $obra)
                                            <option value="{{ $obra->id }}">{{ $obra->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Tipo de Activo</label>
                                <input type="text" class="form-control form-control-sm" name="tipo_activo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Estado</label>
                                <select class="form-select form-select-sm" name="estado" required>
                                    <option value="disponible">Disponible</option>
                                    <option value="en_uso">En Uso</option>
                                    <option value="mantenimiento">Mantenimiento</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Fecha de Registro</label>
                                <input type="date" class="form-control form-control-sm" name="fecha_registro" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Responsable</label>
                                <select class="form-select form-select-sm" name="user_id" required>
                                    @foreach($responsables as $responsable)
                                        <option value="{{ $responsable->id }}">{{ $responsable->name }}</option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="col-md-12">
                                <label class="form-label small mb-1">Descripción</label>
                                <textarea class="form-control form-control-sm" name="descripcion" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editActivoModal" tabindex="-1" aria-labelledby="editActivoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2 bg-light">
                    <h6 class="modal-title" id="editActivoModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Activo
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editActivoForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-3">
                        <div class="row g-3">
                            <div class="col-md-12">
                                    <label class="form-label small mb-1">Obra (Opcional)</label>
                                    <select class="form-select form-select-sm" name="obra_id" id="edit_obra_id">
                                        <option value="">Sin asignar</option>
                                        @foreach($obras as $obra)
                                            <option value="{{ $obra->id }}">{{ $obra->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Tipo de Activo</label>
                                <input type="text" class="form-control form-control-sm" name="tipo_activo" id="edit_tipo_activo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Estado</label>
                                <select class="form-select form-select-sm" name="estado" id="edit_estado" required>
                                    <option value="disponible">Disponible</option>
                                    <option value="en_uso">En Uso</option>
                                    <option value="mantenimiento">Mantenimiento</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Fecha de Registro</label>
                                <input type="date" class="form-control form-control-sm" name="fecha_registro" id="edit_fecha_registro" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Responsable</label>
                                <select class="form-select form-select-sm" name="user_id" id="edit_user_id" required>
                                    @foreach($responsables as $responsable)
                                        <option value="{{ $responsable->id }}">{{ $responsable->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label small mb-1">Descripción</label>
                                <textarea class="form-control form-control-sm" name="descripcion" id="edit_descripcion" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i>Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#activosTable').DataTable({
                responsive: true,
                language: {
                    decimal: "",
                    emptyTable: "No hay datos disponibles",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    infoPostFix: "",
                    thousands: ",",
                    lengthMenu: "Mostrar _MENU_ registros",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar:",
                    zeroRecords: "No se encontraron registros coincidentes",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    aria: {
                        sortAscending: ": activar para ordenar la columna ascendente",
                        sortDescending: ": activar para ordenar la columna descendente"
                    }
                },
                dom: '<"flex justify-between items-center"lf>rtip'
            });
        });

        function openCreateModal() {
            const modal = new bootstrap.Modal(document.getElementById('createActivoModal'));
            modal.show();
        }

        function openEditModal(id) {
            fetch(`/activos/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editActivoForm').action = `/activos/${id}`;
                    document.getElementById('edit_nombre').value = data.nombre;
                    document.getElementById('edit_tipo_activo').value = data.tipo_activo;
                    document.getElementById('edit_descripcion').value = data.descripcion;
                    document.getElementById('edit_estado').value = data.estado;
                    
                    // Formatear fecha_registro (yyyy-mm-dd)
                    const fechaRegistro = data.fecha_registro ? data.fecha_registro.split('T')[0] : '';
                    document.getElementById('edit_fecha_registro').value = fechaRegistro;
                    
                    document.getElementById('edit_user_id').value = data.user_id;
                    document.getElementById('edit_obra_id').value = data.obra_id || ''; // Set obra_id, default to empty if null
                    
                    const modal = new bootstrap.Modal(document.getElementById('editActivoModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del activo');
                });
        }

        function deleteActivo(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este activo?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/activos/${id}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endpush
</x-app-layout>
