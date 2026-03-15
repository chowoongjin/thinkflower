@if ($paginator->hasPages())
    <nav class="pagination-wrap">
        <ul class="pagination">
            {{-- 이전 페이지 --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <a href="#" class="page-link">‹</a>
                </li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-link">‹</a>
                </li>
            @endif

            {{-- 페이지 번호 --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled">
                        <a href="#" class="page-link">{{ $element }}</a>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <a href="#" class="page-link">{{ $page }}</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- 다음 페이지 --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-link">›</a>
                </li>
            @else
                <li class="page-item disabled">
                    <a href="#" class="page-link">›</a>
                </li>
            @endif
        </ul>
    </nav>
@endif
