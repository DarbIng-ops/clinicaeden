@if ($paginator->hasPages())
<div class="d-flex flex-column align-items-center mt-3 mb-1">

    <nav aria-label="Paginación">
        <ul class="pagination pagination-sm mb-1">

            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link px-2">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link px-2" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Elementos de paginación --}}
            @foreach ($elements as $element)
                {{-- Separador "..." --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link px-2">{{ $element }}</span>
                    </li>
                @endif

                {{-- Números de página --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link px-2">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link px-2" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link px-2" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link px-2">&rsaquo;</span>
                </li>
            @endif

        </ul>
    </nav>

    <small class="text-muted">
        Mostrando {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
        de {{ $paginator->total() }} registros
    </small>

</div>
@endif
