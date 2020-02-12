@extends('layouts.head')

@section('content')
	<table class="table">
		<thead>
			<tr class="table-header bg-green">
				<th class="py-3 forum-section"><h4>{{ __('Forum section') }}</h4></th>
				<th class="py-3 latest-post"><h4>{{ __('Latest post') }}</h4></th>
				<th class="py-3 text-center"><h4>{{ __('Threads') }}</h4></th>
				<th class="py-3 text-center"><h4>{{ __('Posts') }}</h4></<h4>
			</tr>
		</thead>
		<tbody>
			@foreach ($tableCategories as $tableCategory)
				<tr class="table-splitter"></tr>
				<tr class="tablecategory bg-dark pt-2">
					<th class="tablecategory-title color-white">
						<h5>{{ __($tableCategory->title) }}</h5>
					</th>
					<th></th> <th></th> <th></th> {{-- to make sure the row is full width, because tables --}}
				</tr>
				@foreach ($tableCategory->tableSubcategories as $tableSubcategory)
					<tr>
						<td>
							<i class="fas fa-folder-open mr-1"></i>
							<a href="
								{{route('tablesubcategory_show', [$tableSubcategory->title, $tableSubcategory->id])}}
							">{{ __($tableSubcategory->title) }}</a>
						</td>
						<td>
							@if (count($tableSubcategory->threads))
								<?php
									foreach ($tableSubcategory->threads as $thread) {
										$postAmount = count($thread->posts);
										foreach ($thread->posts->sortBy('created_at')->take(1) as $p) {
											$post = $p;
										}
									}
								?>
								<p>
									<a href="{{route('post_show', [$post->thread->title, $post->thread->id, $post->id])}}">{{ $post->thread->title }}</a>
								</p>
								<p class="post-created-by">
									<span>{{ __('By') }}</span>
									<a href="{{route('user_show', [$thread->user->id])}}"> {{ $post->user->username }}</a>
									{{ pretty_date($post->created_at) }}
								</p>
							@endif
						</td>
						<td class="text-center">{{ count($tableSubcategory->threads) }}</td>
						<td class="text-center">{{ $postAmount ?? 0 }}</td>
						<?php unset($postAmount); ?>
					</tr>
				@endforeach
			@endforeach
		</tbody>
	</table>
@endsection