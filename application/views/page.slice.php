@extends('index')

@section('title', 'Welcome to CodeIgniter - Page file extending index file')

@section('top_content')
	@parent

	<p>This paragraph was add in the page 'page.slice.php'</p>
@endsection

@section('content')
	<hr>

	<h3>If Statements:</h3>
	<code>&#64;if (count($users) === 1) <br>  &nbsp; I have one user! <br> &#64;elseif (count($users) > 1) <br>  &nbsp; I have multiple users! <br> &#64;else <br>  &nbsp; I don't have any user! <br> &#64;endif</code>
	<p>This example would result:</p>
	@if (count($users) === 1)
		<p>I have one user!</p>
	@elseif (count($users) > 1)
		<p>I have multiple users!</p>
	@else
		<p>I don't have any user!</p>
	@endif
	<br>
	<p>For convenience, Slice also provides an <mark>@unless</mark> directive:</p>
	<code>&#64;unless ($members) <br> &nbsp; You are not signed in. <br> &#64;endunless</code>
	<p>This example would result:</p>
	@unless ($members)
		There are no members at the moment.
	@endunless
	<br>
	<p>In addition to the conditional directives already discussed, you may use the <mark>&#64;isset</mark> and <mark>&#64;empty</mark> directives as convenient shortcuts for their respective PHP functions:</p>
	<code>&#64;isset($rows) <br>   //  $rows is defined and is not null... <br> &#64;endisset <br><br>&#64;empty($rows) <br>   //  $rows is "empty"... <br> &#64;endempty</code>

	<hr>

	<h3>Loopings:</h3>
	<code>&#64;for ($i = 0; $1 < 10; $i++) <br> &nbsp; The current value is &#123;&#123; $i }} <br> &#64;endfor</code>
	<p>This example would result:</p>
	<ul>
	@for ($i = 0; $i < 10; $i++)
		<li>The current value is {{ $i }}</li>
	@endfor
	</ul>
	<br>
	<code>&#64;foreach ($users as $user) <br> &nbsp; This is user &#123;&#123; $user['name'] }} <br> &#64;endforeach</code>
	<p>This example would result:</p>
	<ul>
		@foreach ($users as $user)
			<li>This is user {{ $user['name'] }}</li>
		@endforeach
	</ul>
	<br>
	<code>&#64;forelse ($members as $member) <br> &nbsp;&nbsp; &#123;&#123; $member['name'] }} <br> &#64;empty <br> &nbsp;&nbsp; No members <br> &#64;endforelse<br> </code>
	<p>This example would result:</p>
	<ul>
		@forelse ($members as $member)
			<li>{{ $member['name'] }}</li>
		@empty
			<li>No members</li>
		@endforelse
	</ul>
	<br>
	<p>When using loops you may also end the loop or skip the current iteration:</p>
	<code>&#64;foreach ($users as $user) <br> &nbsp; &#64;if ($user['id'] == 1) <br> &nbsp; &nbsp; &#64;continue <br> &nbsp; &#64;endif <br><br> &nbsp; &nbsp; &#123;&#123; $user['name'] }} <br><br> &nbsp; &#64;if ($user['id'] == 5) <br> &nbsp; &nbsp; &#64;break <br> &nbsp; &#64;endif <br> &#64;endforeach</code>
	<p>This example would result:</p>
	<ul>
		@foreach ($users as $user)
			@if ($user['id'] == 1)
				@continue
			@endif

			<li>{{ $user['name'] }} - {{ $user['id'] }}</li>

			@if ($user['id'] == 5)
				@break
			@endif
		@endforeach
	</ul>
	<p>You may also include the condition with the directive declaration in one line:</p>
	<code>&#64;foreach ($users as $user) <br>  &nbsp;  &nbsp; &#64;continue($user['id'] == 2) <br> <br> &nbsp; &nbsp; &#123;&#123; $user['name'] }} - &#123;&#123; $user['id'] }} <br> <br> &nbsp; &nbsp; &#64;break($user['id'] == 4) <br> &#64;endforeach</code>
	<p>This example would result:</p>
	<ul>
		@foreach ($users as $user)
			@continue($user['id'] == 2)

			<li>{{ $user['name'] }} - {{ $user['id'] }}</li>

			@break($user['id'] == 4)
		@endforeach
	</ul>

	<hr>

	<h3>Setup your own directives:</h3>
	<p>Slice allows you to define your own custom directives using the <mark>directive</mark> method. When the Slice compiler encounters the custom directive, it will call the provided callback with the expression that the directive contains.</p>
	<p>The following example creates a <mark>@slice($var)</mark> directive that only display the text of the $var. This is a method in the controller of the CodeIgniter:</p>
	<code>static function custom_slice_directive($content) <br> { <br>  &nbsp; &nbsp; //	Finds for @slice directive <br>  &nbsp; &nbsp; $pattern = '/(\s*)@slice\s*\((\'.*\')\)/'; <br>  &nbsp; &nbsp; return preg_replace($pattern, '$1<?php echo "$2"; ?>', $content); <br> } </code>
	<p>Then you have to use it with the method <mark>directive</mark>, like this:</p>
	<code>$this->slice->directive('Test::custom_slice_directive');</code>
@endsection
