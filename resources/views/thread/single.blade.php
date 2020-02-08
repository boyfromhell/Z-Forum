@extends('layouts.head')

@section('content')
	<p class="breadcrumb">
		<a href="/">Home</a> 
		<span class="mx-1">&raquo;</span>
		<a href="
			{{route('tablecategory_show', [$thread->tableSubcategory->tableCategory->title, $thread->tableSubcategory->tableCategory->id])}}
		">{{ $thread->tableSubcategory->tableCategory->title }}</a>
		<span class="mx-1">&raquo;</span>
		<a href="
			{{route('tablesubcategory_show', [$thread->tableSubcategory->title, $thread->tableSubcategory->id])}}
		">{{ $thread->tableSubcategory->title }}</a>
		<span class="mx-1">&raquo;</span>
		<span>{{ $thread->title }}</span>
	</p>

	<div class="thread">
		@foreach ($thread->posts as $post)
		<?php if (!isset($i)) $i = 0; ?>
		<?php $i++; ?>
			<div class="post <?php if ($thread->user->id === $post->user->id) echo 'is_author'; ?>" id="post-{{$post->id}}">
				<div class="post-banner row m-0 justify-content-between">
					<span class="post-date px-2 color-white">
						<?php $formatted = explode(' ', $post->created_at); echo $formatted[0] . ', ' . $formatted[1]; ?>
					</span>
					<span class="post-permalink px-2">
						<a href="{{route('post_permalink', [$post->id])}}">Permalink</a>
					</span>
				</div>
				<div class="post-content px-2">
					<?php echo $post->content ?>
				</div>
			</div>
			@if ($i < count($thread->posts))
				<div class="post-dividers d-flex">
					<div class="post-divider"></div>
					<div class="post-divider-small"></div>
					<div class="post-divider-middle"></div>
					<div class="post-divider-small"></div>
					<div class="post-divider"></div>
				</div>
			@endif
		@endforeach
	</div>
	<a href="{{route('post_create', [$thread->title, $thread->id])}}">
		<button class="btn btn-primary color-white" type="button">{{ __('Reply') }}</button>
	</a>
@endsection