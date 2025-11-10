@php
  $items = isset($visibleMuebles) ? collect($visibleMuebles) : (isset($muebles) ? collect($muebles) : collect());
  $isAdminFlag = !empty($isAdmin) && $isAdmin;
  if (! $isAdminFlag) {
      $items = $items->filter(function($m){
          return is_null($m->persona_id);
      })->values();
  }
@endphp

<div class="grid" role="list">
  @foreach($items as $m)
    <div class="card" role="listitem" data-id="{{ $m->id }}">
      <div class="card-inner">
        <div class="card-media">
          <img src="{{ $m->ruta_img ? url($m->ruta_img) : asset('imgs/default.webp') }}" alt="{{ $m->codigo ?? 'mueble' }}" onerror="this.src='{{ asset('imgs/default.webp') }}'">
        </div>
        <div class="card-info">
          <div class="card-top">
            <div class="card-title">{{ $m->codigo ?? 'ID '.$m->id }} — {{ \Illuminate\Support\Str::limit($m->descripcion ?? '-', 80) }}</div>
            <div><span class="estado-badge estado-{{ $m->estado ?? '' }}">{{ ucfirst(str_replace('_',' ', $m->estado ?? '-')) }}</span></div>
          </div>

          @if(!empty($m->marca) || !empty($m->modelo))
            <div class="card-brand">
              @if(!empty($m->marca)) <div class="marca">{{ $m->marca }}</div> @endif
              @if(!empty($m->modelo)) <div class="modelo">{{ $m->modelo }}</div> @endif
             @if(!empty($m->categoria))
               <div class="categoria">{{ optional($m->categoria)->nombre }}</div>
             @endif
            </div>
          @endif

          <div class="card-meta">
            @if(!empty($isAdmin) && $isAdmin)
              <div class="card-price">${{ number_format($m->monto_unitario ?? 0, 2, ',', '.') }}</div>
              <div class="card-responsable"><strong>Responsable:</strong> {{ $m->responsable ? ($m->responsable->nombre . ' ' . $m->responsable->apellido) : 'ninguno' }}</div>
            @endif
            <div class="card-solicitante"><strong>Solicitante:</strong> {{ $m->usuario ? ($m->usuario->nombre . ' ' . $m->usuario->apellido) : 'ninguno' }}</div>
          </div>

          @php $commentsToShow = ($m->comentarios ?? collect())->take(3); @endphp
          <div class="card-comments">
            <strong>Comentarios:</strong>
            @if($commentsToShow->isNotEmpty())
              @foreach($commentsToShow as $c)
                @php $randColor = 'hsl('.rand(0,360).' '.rand(0,6).'% '.(90+rand(0,8)).'%)'; @endphp
                <div class="comment small" style="background: {{ $randColor }};">
                  <div class="author">{{ $c->usuario ? ($c->usuario->nombre . ' ' . $c->usuario->apellido) : 'anonimo' }}</div>
                  <div class="text">{{ \Illuminate\Support\Str::limit($c->comentario, 200) }}</div>
                </div>
              @endforeach
              @if(($m->comentarios->count() ?? 0) > 3)
                <div class="comment more">+{{ $m->comentarios->count() - 3 }} más</div>
              @endif
            @else
              <div class="comment small">ninguno</div>
            @endif
          </div>

          <div class="card-actions">
            <a href="{{ url('/muebles/'.$m->id) }}" class="btn-ghost" style="text-decoration:none;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;">Ver detalle</a>

            @if(!empty($isAdmin) && $isAdmin)
              <div style="display:inline-flex;gap:8px;align-items:center;">
                <button type="button" class="btn-edit" data-mueble='@json($m)'>Editar</button>
                <form method="POST" action="{{ url('/muebles/'.$m->id) }}" style="margin:0;padding:0;">
                  @csrf @method('DELETE')
                  <button type="button" class="btn-delete" data-id="{{ $m->id }}" data-confirm="¿Eliminar mueble {{ $m->codigo ?? $m->id }}?" data-confirm-callback="confirmDeleteById">Eliminar</button>
                </form>
              </div>
            @else
              <a class="btn-base btn-new" href="{{ url('/solicitudes/create?mueble_id=' . $m->id) }}">Solicitar</a>
            @endif
          </div>

        </div>
      </div>
    </div>
  @endforeach
</div>

@if(empty($isAdmin) || !$isAdmin)
<script>
document.addEventListener('DOMContentLoaded', function(){
  try {
    const sel = document.querySelector('form#filters select[name="persona_id"], form#sol-filters select[name="persona_id"]');
    if (sel) {
      const container = sel.closest('div') || sel.parentElement;
      if (container) container.style.display = 'none';
      const form = sel.closest('form');
      if (form && !form.querySelector('input[name="persona_id_hidden_for_none"]')) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'persona_id';
        input.value = 'none';
        input.setAttribute('data-added-by-js','1');
        form.appendChild(input);
      }
    }
  } catch(e){ console.error('hide persona_id filter error', e); }
});
</script>
@endif