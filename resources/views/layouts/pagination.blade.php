@if ($paginator->hasPages())
    <div class="pagination" role="navigation" data-current-page="{{$paginator->currentPage()}}">
        {{-- Previous Page Link --}}
        @if (!$paginator->onFirstPage())
			<div class="item-wrapper">
				<a class="item" href="{{ $paginator->previousPageUrl() }}" rel="prev"> 
					<i class="fas fa-chevron-left mr-1"></i>
					{{ __('Prev') }}
				</a>
			</div>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
				<div class="item-wrapper">
                	<a class="item dots" href="#" id="pagination-dots">{{ $element }}</a>
				</div>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
						<div class="item-wrapper">
                        	<a class="item active" href="{{ $url }}" aria-current="page">{{ $page }}</a>
						</div>
                    @else
						<div class="item-wrapper">
							<a class="item" href="{{ $url }}">{{ $page }}</a>
						</div>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
			<div class="item-wrapper">
				<a class="icon item" href="{{ $paginator->nextPageUrl() }}" rel="next">
					{{ __('Next') }}
					<i class="fas fa-chevron-right ml-1"></i>
				</a>
			</div>
        @endif
    </div>
@endif