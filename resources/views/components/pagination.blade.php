@props([
  'paginator',
  'perPageOptions' => [10, 25, 50, 100],
  'ariaLabel' => 'Pagination'
])
@php
  $current = $paginator->currentPage();
  $last = $paginator->lastPage();
  $firstItem = $paginator->firstItem() ?? 0;
  $lastItem = $paginator->lastItem() ?? 0;
  $total = $paginator->total() ?? 0;
  $perPage = $paginator->perPage();
  $start = max(1, $current - 2);
  $end = min($last, $current + 2);
@endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
  <div class="text-muted small">
    Showing {{ $firstItem }}–{{ $lastItem }} of {{ $total }} · Page {{ $current }} of {{ $last }}
  </div>
  <form method="get" class="d-inline-flex align-items-center gap-2">
    @foreach(request()->except(['per_page','page']) as $k => $v)
      @if(is_array($v))
        @foreach($v as $vv)
          <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
        @endforeach
      @else
        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
      @endif
    @endforeach
    <label class="form-label mb-0 small" for="perPageSelect">Items per page</label>
    <select id="perPageSelect" name="per_page" class="form-select form-select-sm" style="width:auto; min-width: 110px" onchange="this.form.submit()">
      @foreach($perPageOptions as $opt)
        <option value="{{ $opt }}" @selected($perPage==$opt)>{{ $opt }}</option>
      @endforeach
    </select>
    <input type="hidden" name="page" value="1">
  </form>
  <nav aria-label="{{ $ariaLabel }}">
    <ul class="pagination mb-0">
      <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
        <a class="page-link" href="{{ $paginator->previousPageUrl() ?: '#' }}" rel="prev" aria-label="Previous">Previous</a>
      </li>
      @if($start > 1)
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
        </li>
        @if($start > 2)
          <li class="page-item disabled"><span class="page-link">…</span></li>
        @endif
      @endif
      @for($i = $start; $i <= $end; $i++)
        <li class="page-item {{ $i === $current ? 'active' : '' }}">
          @if($i === $current)
            <span class="page-link">{{ $i }}</span>
          @else
            <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
          @endif
        </li>
      @endfor
      @if($end < $last)
        @if($end < $last - 1)
          <li class="page-item disabled"><span class="page-link">…</span></li>
        @endif
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->url($last) }}">{{ $last }}</a>
        </li>
      @endif
      <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
        <a class="page-link" href="{{ $paginator->nextPageUrl() ?: '#' }}" rel="next" aria-label="Next">Next</a>
      </li>
    </ul>
  </nav>
</div>
