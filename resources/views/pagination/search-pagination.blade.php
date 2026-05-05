<ul class="pagination" role="navigation">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <li class="page-item d-inline-block " aria-disabled="true">
            <span class="page-link" style="    cursor: default;" rel="prev"><i class="fal"></i></span>
        </li>
    @else
        <li class="page-item d-inline-block">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}"
               rel="prev" data-page="{{ $paginator->currentPage()-1 }}">
                <i class="fal"></i>
            </a>
        </li>
    @endif
    <li class="page-item d-inline-block number-input">
        <input
            class="form-control"
            min="1" step="1" max="{{ $paginator->lastPage() }}" onkeypress='validate_number(event)'
            type="text" aria-label="Change page" value="{{ $paginator->currentPage() }}">
    </li>
    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <li class="page-item d-inline-block">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
               data-page="{{ $paginator->currentPage()+1 }}">
                <i class="fal"></i>
            </a>
        </li>
    @else
        <li class="page-item d-inline-block " aria-disabled="true">
            <span class="page-link" style="cursor: default;"  rel="next"><i class="fal"></i></span>
        </li>
    @endif
</ul>
