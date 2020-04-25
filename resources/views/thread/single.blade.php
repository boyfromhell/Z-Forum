{{-- Passed variables: $thread --}}
@extends('head')

@section('pageTitle')
	{{ $thread->title }}
@endsection

@section('breadcrumbs')
	{{ Breadcrumbs::render('thread', $thread) }}
@endsection

@can('update', $thread)
	@section('crudToolbar')
		<div class="crud-toolbar">
			<button class="btn btn-default thread-edit">
				<i class="fas fa-pen"></i>
			</button>
			@if ($thread->locked)
				<button class="btn btn-default spin thread-toggle" type="button">
					<i class="fas fa-unlock"></i>
				</button>
			@else
				<button class="btn btn-default spin thread-toggle" type="button">
					<i class="fas fa-lock"></i>
				</button>
			@endif
			@can('delete', $thread)
				<button class="btn btn-hazard spin thread-delete" type="submit">
					<i class="fas fa-trash-alt"></i>
				</button>
			@endcan
		</div>
	@endsection
@endcan

@section('threadTitle')
	<div class="thread-header">
		<h4 class="thread-title">{{ $thread->title }}</h4>
	</div>

	<div class="pagination-upper">
		{{ $posts->links('layouts.pagination') }}
	</div>

	@auth
		@can('create', [App\Post::class, $thread])
			<form class="d-none" id="reply-form" action="{{route('post_store', [$thread->id, $thread->slug])}}" method="POST">
				@csrf
				<textarea class="d-none" id="thread-reply" name="content"></textarea>
				<div class="post-toolbar">
					<button class="btn btn-success-full spin reply-save" disabled>
						<span>{{ __('Send') }}</span>
					</button>
					<button class="btn btn-default reply-cancel" type="button">
						<span>{{ __('Cancel') }}</span>
					</button>
				</div>
			</form>
		@endcan
	@endauth
@endsection

@section('content')
	<div class="thread @if ($thread->locked) locked @endif">
		@auth
			@can('create', [App\Post::class, $thread])
				<button class="reply-button btn btn-success-full" type="button">
					{{ __('Reply') }}
				</button>
			@endcan
		@endauth

		{{-- Don't even ask, it just works --}}
		@php $i = ($posts->currentPage() - 1) * $posts->perPage() + 1; @endphp

		@foreach ($posts as $post)
			@component('components.post', ['post' => $post, 'i' => $i])
				
			@endcomponent
			@php $i++; @endphp
		@endforeach
	</div>

	@auth
		@can('create', [App\Post::class, $thread])
			<div id="quick-reply">
				<form action="{{route('post_store', [$thread->id, $thread->slug])}}" method="POST">
					@csrf
					<textarea type="text" name="content" id="form-content"></textarea>
					@error('content') <p class="color-red">{{ $message }}</p> @enderror
					<button class="btn post-send spin btn-success-full" type="submit" disabled>
						<span>{{ __('Send') }}</span>
					</button>
				</form>
			</div>

			<script>
				let interval = setInterval(() => {
					if ($('#quick-reply').find('iframe').length) {
						clearInterval(interval);
						let iframe = $('#quick-reply').find('iframe')[0];
						let iframeWindow = iframe.contentWindow.document;
						let input = $(iframeWindow).find('body');

						// Style p elements inside to match the rest of the site
						$(input).find('p').css('margin', '0');
						$(input).css('line-height', '1.5');

						input.on('input change', function() {
							$(this).find('p').css('margin', '0');

							if ($(this).html() !== '<p><br></p>' && $(this).html() !== '') {
								$('#quick-reply button').each(function() {
									$(this).removeAttr('disabled');
								});
							} else {
								$('#quick-reply button').each(function() {
									$(this).attr('disabled', true);
								});
							}
						});
					}
				}, 50);

				let interval2 = setInterval(() => {
					if ($('#reply-form').find('iframe').length) {
						clearInterval(interval2);
						let iframe = $('#reply-form').find('iframe')[0];
						let iframeWindow = iframe.contentWindow.document;
						let input = $(iframeWindow).find('body');

						// Style p elements inside to match the rest of the site
						$(input).find('p').css('margin', '0');
						$(input).css('line-height', '1.5');

						input.on('input change', function() {
							$(this).find('p').css('margin', '0');

							if ($(this).html() !== '<p><br></p>' && $(this).html() !== '') {
								$('.reply-save').removeAttr('disabled');
							} else {
								$('.reply-save').attr('disabled', true);
							}
						});
					}
				}, 50);

				$('.reply-button').click(function() {
					// Spawn dummy textarea for TinyMCE if it doesn't already exist, then spawn the editor
					if ($('#reply-form').hasClass('d-none')) {
						$('#reply-form').removeClass('d-none');
					}

					if (!$(this).hasClass('d-none')) {
						$(this).addClass('d-none');
					}
					
					tinymce.init({
						selector: '#thread-reply',
						plugins: 'advlist autolink lists link image media charmap print preview hr anchor pagebreak bbcode code',
						height: 300,
					});

					// Since the callback doesn't work we have to check until the editor is initialized
					let interval = setInterval(() => {
						// We have found the editor
						if ($('#reply-form .tox-tinymce').length) {
							// Since it's an iframe we need to access it this way before doing .focus()
							let iframe = $('#reply-form iframe')[0];
							let iframeWindow = iframe.contentWindow.document;

							$(iframeWindow).find('body').focus();

							clearInterval(interval);
						}
					}, 50);
				});

				$('.reply-cancel').click(function() {
					// Reset things
					$('.reply-button').removeClass('d-none');
					$('#reply-form').addClass('d-none');
				});
			</script>
		@endcan

		@can('create', [App\Post::class, $post->thread])
			<script>
				function post_quote(element) {
					let iframes = $('#reply-form, #quick-reply, .post').find('iframe');
					let post =  element.parents('.post');
					let link = post.find('.permalink').attr('href');
					let author = post.find('.post-author a').html();
					let content = post.find('.post-body').html();

					iframes.each(function() {
						let iframe = $(this)[0].contentWindow.document;
						$(iframe).find('body').css('line-height', '1.5').prepend(`
							<p style="margin: 0;">
								Posted by ${author} - <a href="${link}">Read post</a>
							</p>
							
							[quote]${content}[/quote]
						`);
					});
				}

				$('.post .post-quote').click(function() {
					post_quote($(this));
				});
			</script>
		@endcan

		@include('js.post.controls')

		@can('update', $thread)
			<script>
				// Init the handlers
				thread_handlers();

				// Toggle thread lock/unlock state
				$('.thread-toggle').click(function(e) {
					e.preventDefault();
					$.ajax({
						url: '{{ route("thread_toggle") }}',
						method: 'POST',
						data: {
							_token: '{{ Session::token() }}',
							id: '{{ $thread->id }}',
						},
						success: function(response) {
							$('.thread-toggle').removeClass('loading');
							ajax_alert(response);

							// Need to delay this due to .spin event handler code being fired after this 
							setTimeout(() => {
								$('.thread-toggle').removeAttr('disabled');
							}, 100);

							if (response.state === 'unlocked') {
								$('.thread-toggle i').removeClass('fa-lock').addClass('fa-lock color-white')
							} else {
								$('.thread-toggle i').removeClass('fa-lock').addClass('fa-unlock color-white');
							}
						},
						error: function(error) {
							console.log(error);
						}
					});
				});

				// Edit thread title
				function thread_edit() {
					if (!$('.thread-save-toolbar').length) {
						let title = $('.thread-title').html();

						$('.thread-title').replaceWith(`
							<fieldset style="width: ${$('#main').width()}px;">
								<legend>
									Title
								</legend>
								<input type="text" value="${title}" />
							</fieldset>
						`);
						$('.thread-header').append(`
							<div class="thread-save-toolbar">
								<button class="btn btn-success-full spin thread-save">
									<span>Save</span>
								</button>
								<button class="btn btn-default thread-cancel">
									<span>Cancel</span>
								</button>
							</div>
						`);
					}
				}

				// Cancel the edited thread and reset elements to how they were before
				function thread_cancel(original) {					
					// Reset thread header and edit button
					$('.thread-header').html(original);
					$('.thread-edit').removeAttr('disabled');

					// The event handler needs to be re-initalized since the element was destroyed
					thread_handlers();
				}

				// Save the edited post and reset elements to how they were before, but with the updated post content
				function thread_save(original, e) {
					e.preventDefault();
					$.ajax({
						url: '{{ route("thread_update_ajax") }}',
						method: 'POST',
						data: {
							_token: '{{ Session::token() }}',
							id: '{{ $thread->id }}',
							title: $('.thread-header input').val()
						},
						success: function(response) {
							// Reset post element and remove TinyMCE editor first
							$('.thread-header').html(original);

							// Insert the newly edited content into the post
							$('.thread-title').html(response.title);

							$('.thread-edit').removeAttr('disabled');

							// Edit the active breadcrumb content
							$('.breadcrumb-item.active').html(response.title);

							// Edit the current URL state for better UX in case user reloads, otherwise it will go to the old item URL
							window.history.pushState("", "", response.url);

							// Dispay the alert message on the top of the page
							if (response.type !== 'none') {
								ajax_alert(response);
							}

							// The event handlers need to be reinitalized since the element was destroyed
							thread_handlers();
						},
						error: function(error) {
							console.log(error);
						}
					});
				}

				// Initialize post handlers in order to let them be "recursive"
				function thread_handlers() {
					let original = $('.thread-header').html();

					$('.thread-edit').click(function() {
						thread_edit();

						$('.thread-save').click(function(e) {
							thread_save(original, e);
						});

						$('.thread-cancel').click(function() {
							thread_cancel(original);
						});

						// Put disabled on edit button
						$(this).attr('disabled', true);
					});

					$('.thread-delete').click(function(e) {
						thread_delete(e);
					});
				}
			</script>
		@elsecan ('delete', $thread)
			<script>
				// Delete thread
				function thread_delete(e) {
					e.preventDefault();
					$.ajax({
						url: '{{ route("thread_delete_ajax") }}',
						method: 'POST',
						data: {
							_token: '{{ Session::token() }}',
							id: '{{ $thread->id }}',
						},
						success: function(response) {
							window.location.href = response.redirect;
						},
						error: function(error) {
							console.log(error);
						}
					});
				}
			</script>
		@endcan
	@endauth

	@if (settings_get('posts_per_page') >= 20)
		{{ $posts->links('layouts.pagination') }}
	@endif
@endsection