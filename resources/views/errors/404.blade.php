{{-- Passed variables: $value --}}
@extends('layouts.head')

@section('pageTitle')
	404
@endsection

@php $disableSidebar = true; @endphp

@section('content')
	<div class="page-error" id="four-zero-four">
		<div class="header">
			<h1>
				<span>4</span>
				<i class="fas fa-ghost"></i>
				<span>4</span>
			</h1>

			<h2>{{ __('Not Found') }}</h2>

			<form action="{{route('search')}}" method="get">
				@csrf
				<div class="search">
					<input type="text" name="search" id="search-error" placeholder="What are you looking for?"
						@isset ($value) value="{{$value}}" @endisset
					/>
					<button class="btn btn-success" type="submit">
						<span>{{ __('Search') }}</span>
						<i class="fas fa-search"></i>
					</button>
				</div>
			</form>

			<iframe width="800" height="400" src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ" frameborder="0"></iframe>
		</div>
	</div>
@endsection