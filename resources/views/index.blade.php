@extends('layouts.head')

@section('content')
	<div class="table-wrapper">
		<table class="table">
			<thead>
				<tr class="table-header">
					<th class="py-3 forum-section"><h4>{{ __('Forum section') }}</h4></th>
					<th class="py-3 latest-post"><h4>{{ __('Latest post') }}</h4></th>
					<th class="py-3 threads text-center"><h4>{{ __('Threads') }}</h4></th>
					<th class="py-3 posts text-center"><h4>{{ __('Posts') }}</h4></<h4>
				</tr>
			</thead>
			<tbody>
				@foreach ($tableCategories as $tableCategory)
					<tr class="table-category">
						<th class="tablecategory-title">
							<h5>
								<a href="{{route('tablecategory_show', [$tableCategory->id, $tableCategory->slug])}}">
									{{ __($tableCategory->title) }}
								</a>
							</h5>
						</th>
						<th></th> <th></th> <th></th> {{-- to make sure the row is full width, because tables --}}
					</tr>
					@foreach ($tableCategory->tableSubcategories as $tableSubcategory)
						<tr class="table-row">
							<td>
								<div class="d-flex">
									<i class="fas fa-folder-open mr-2"></i>
									<a href="{{route('tablesubcategory_show', [$tableSubcategory->id, $tableSubcategory->slug])}}">
										{{ __($tableSubcategory->title) }}
									</a>
								</div>
							</td>
							<td>
								@if (isset($tableSubcategory->threads))
									<?php
										foreach ($tableSubcategory->threads as $thread) {
											$postAmount = count($thread->posts);
											foreach ($thread->posts->sortByDesc('updated_at')->take(1) as $p) {
												$post = $p;
											}
										}
									?>
									<p>
										<a href="{{route('post_show', [$post->thread->id, $post->thread->slug, $post->id])}}">{{ $post->thread->title }}</a>
									</p>
									<p class="post-created-by">
										<span>{{ __('By') }}</span>
										<a href="{{route('user_show', [$post->user->username])}}">{{ $post->user->username }}</a>
										{{ pretty_date($post->updated_at) }}
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
	</div>
@endsection