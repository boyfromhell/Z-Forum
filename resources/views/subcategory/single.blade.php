{{-- Passed variables: $subcategory --}}
@extends('head')

@section('pageTitle')
	{{ $subcategory->title }}
@endsection

@section('breadcrumbs')
	{{ Breadcrumbs::render('subcategory', $subcategory) }}
@endsection

@section('content')
	<button class="btn btn-success-full mark-as-read">{{ __('Mark all threads as read') }}</button>

	<div id="table">
		<div class="table-group">
			@component('components.table-header')
				@slot('title')
					{{ $subcategory->title }}
				@endslot
			@endcomponent

			@auth
				@if (count(auth()->user()->visited_threads))
					@foreach (auth()->user()->visited_threads as $visited_thread)
						@php $visitedThreadsIds[] = $visited_thread->thread->id @endphp
					@endforeach
				@else
					@php $visitedThreadsIds = [] @endphp
				@endif
			@endauth

			@foreach ($subcategory->threads as $thread)	
				{{-- Check if the thread has any admin post --}}
				@foreach ($subcategory->posts as $post)
					@if ($post->user->role === 'superadmin')
						@php $admin_post = true @endphp
						@break
					@endif
				@endforeach

				@auth
					{{-- Check if user has any read thread in the subcategory --}}
					@php $read = true @endphp
					@if (!in_array($thread->id, $visitedThreadsIds))
						@php $read = false @endphp
					@else
						@php $latest_post = $thread->posts()->latest()->get()->take(1)[0] @endphp
						@php $visited = auth()->user()->visited_threads->where('thread_id', $latest_post->thread->id) @endphp
						@foreach ($visited as $item)
							@if (strtotime($item->updated_at) - strtotime($latest_post->created_at) <= 0)
								@php $read = false @endphp
								@break
							@endif
						@endforeach
					@endif
				@endauth

				@component('components.table-row', ['admin_post' => $admin_post ?? null, 'read' => $read ?? null, 'disableFolderIcon' => true])
					@slot('title')
						<a href="{{route('thread_show', [$thread->id, $thread->slug])}}">
							{{ $thread->title }}
						</a>
					@endslot

					@slot('views')
						@php $views = 0; @endphp
						@php $views += $thread->views @endphp
						{{ $views }}
					@endslot

					@slot('posts')
						{{ count($thread->posts) }}
					@endslot

					@slot('latest_post')
						@foreach ($thread->posts()->latest()->get() as $post)
							@isset($post->thread)
								<a href="{{
									route('post_show', [
										$post->thread->id,
										$post->thread->slug,
										get_item_page_number($post->thread->posts->sortBy('created_at'), $post->id, settings_get('posts_per_page')),
										$post->id,
									])
								}}">
									{{ pretty_date($post->updated_at) }}
									<i class="fas fa-sign-in-alt"></i>
								</a>
								@break {{-- Since we're in another loop, make sure we only do this one once no matter what --}}
							@endisset
						@endforeach
					@endslot
				@endcomponent
			@endforeach {{-- $threads --}}
		</div>
	</div>
	
	<a class="btn btn-success-full" href="{{route('thread_create', [$subcategory->id, $subcategory->slug])}}">
		<span>{{ __('Create new thread') }}</span>
	</a>

	@auth
		@php $collection = 'subcategory_id' @endphp
		@php $id = $subcategory->id @endphp

		@include('js.mark-as-read')

		<script>
			$('.mark-as-read').click(async function() {
				mark_as_read();
			});
		</script>
	@endauth
@endsection