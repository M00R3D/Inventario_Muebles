<div id="table-wrapper" class="minimal" style="display:none;">
  <table id="table-view" role="table" aria-label="Listado comprimido">
    <thead>
      <tr>
        <th>Código</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Responsable</th>
        <th>Solicitante</th>
        @if($isAdmin)<th class="admin-only">Monto</th>@endif
        <th>Acc.</th>
      </tr>
    </thead>
    <tbody>
      @forelse($muebles as $m)
        <tr>
          <td>{{ $m->codigo ?? 'ID '.$m->id }}</td>
          <td class="small-desc">{{ \Illuminate\Support\Str::limit($m->descripcion ?? '-', 60) }}</td>
          <td><span class="estado-badge estado-{{ $m->estado ?? '' }}">{{ ucfirst(str_replace('_',' ', $m->estado ?? '-')) }}</span></td>
          <td>{{ $m->responsable ? ($m->responsable->nombre . ' ' . $m->responsable->apellido) : 'ninguno' }}</td>
          <td>{{ $m->usuario ? ($m->usuario->nombre . ' ' . $m->usuario->apellido) : 'ninguno' }}</td>
          <td></td>
        </tr>
      @empty
        <tr><td colspan="{{ $isAdmin ? 7 : 6 }}" style="padding:8px;">No hay muebles</td></tr>
      @endforelse
    </tbody>
  </table>
</div>