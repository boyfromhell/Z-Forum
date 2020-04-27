@extends('head')

@section('pageTitle')
	{{ $user->username . ' | ' . __('Likes') }}
@endsection

@section('content')
	@component('components.profile', ['user' => $user, 'posts_with_likes' => $posts_with_likes, 'active' => 'likes'])
		@slot('likes')
			<div class="profile-lower likes">
				@if (count($likes))
					@foreach ($likes as $like)
						@php $post = App\Post::find($like->post_id) @endphp
						@component('components.post', [
							'post'				   => $post,
							'disablePostToolbar'   => true,
							'disablePostBanner'    => true,
							'disablePostSignature' => true
						])
							@slot('banner_link')
								<a href="{{
									route('post_show', [
										$post->thread->id,
										$post->thread->slug,
										get_item_page_number($post->thread->posts->sortBy('created_at'), $post->id, settings_get('posts_per_page')),
										$post->id,
									])
								}}">
									{{ __('Context') }}
									<i class="fas fa-sign-in-alt"></i>
								</a>
							@endslot
						@endcomponent
					@endforeach
				@else
					{{ __('User hasn\'t liked anything yet 👀') }}
				@endif
			</div>
		@endslot

		@slot('pagination')
			{{ $likes->links('layouts.pagination') }}
		@endslot
	@endcomponent
@endsection