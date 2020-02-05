@extends('layouts.app')

@section('content')
	<p class="breadcrumb">
		<a href="/">Home</a> 
		<span class="mx-1">&raquo;</span>
		<a href="
			{{route('tablecategory_show', [$tableSubcategory->tableCategory->title, $tableSubcategory->tableCategory->id])}}
		">{{ $tableSubcategory->tableCategory->title }}</a>
		<span class="mx-1">&raquo;</span>
		<span>{{ $tableSubcategory->title }}</span> 
	</p>

	<table class="table">
		<thead>
			<tr class="table-header bg-pink">
				<th class="py-3"><h4>{{ __('Title') }}</h4></th>
				<th class="py-3"><h4>{{ __('Latest post') }}</h4></th>
				<th class="py-3"><h4>{{ __('Posts') }}</h4></th>
			</tr>
		</thead>
		<tbody>
			<tr class="table-category bg-dark">
				<th><h5>{{ __($tableSubcategory->title) }}</h5></th>
				<th></th><th></th> <!-- to make sure the row is full width, becaues tables -->
			</tr>
			@foreach ($tableSubcategory->threads as $thread)
				<tr>
					<td>
						<a class="thread-link d-flex" href="{{route('thread_show', [$thread->title, $thread->id])}}">{{ __($thread->title) }}</a>
						<p>
							<span>{{ __('By ') }}</span>
							<a class="thread-author-link" href="{{route('user_show', [$thread->user->id])}}">{{ $thread->user->username }}</a>
						</p>
					</td>
					<td>
						<!-- latest post -->
						@foreach ($thread->posts->sortByDesc('created_at')->take(1) as $post)
							<p class="post-created-at">{{ $post->created_at }}</p>
							<p class="post-created-by">{{ __('By ') }}<a href="{{route('user_show', [$thread->user->id])}}">{{ $post->user->username }}</a></p>
						@endforeach
					</td> 
					<td>{{ count($thread->posts) }}</td> <!-- posts -->
				</tr>
			@endforeach
		</tbody>
	</table>
	<a href="{{route('thread_create', [$tableSubcategory->title, $tableSubcategory->id])}}">New</a>
@endsection