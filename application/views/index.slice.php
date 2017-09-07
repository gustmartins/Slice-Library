@php
	defined('BASEPATH') OR exit('No direct script access allowed');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>@yield('title', 'Welcome to CodeIgniter')</title>

	@include('_css')
	@includeIf('_js')
</head>
<body>

<div id="container">
	@yield('page_title', '<h1>Welcome to CodeIgniter Slice-Library</h1>')

	<div id="body">
		@section('top_content')
			<p>This {{ anchor('', 'paragraph') }} belongs to a section called 'top content'! It must always be shown! Sometimes other pages will add another paragraph to this section. <br> You can open a section like this:</p>
			<code>&#64;section('top_section')</code>
			<p>You need to close the section with the directive:</p>
			<code>&#64;endsection</code>
			<p>If you want to close the section and display it, use this directive:</p>
			<code>&#64;show</code>
		@show

		<hr>

		<h3>Comments:</h3>
		<p>Slice allows you to define comments in your views. However, unlike HTML comments, Blade comments are not included in the HTML returned by your application. <br> There is a PHP comment between this paragraph and the code example bellow.</p>
		{{-- This is a Slice comment --}}
		<code>@{{&#45;&#45; This is a Slice comment --}} </code>

		<br><hr>

		<h3>PHP Tags:</h3>
		<p>In some situations, it's useful to embed PHP code into your views. You can use the Slice <mark>&#64;php</mark> directive to execute a block of plain PHP within your template. <br> In this page you can see an example in the begging of the page.</p>
		<code>&#64;php <br> // <br> &#64;endphp</code>

		<br><hr>

		<h3>Echoing variables:</h3>
		<p>You may display data passed to your Slice views by wrapping the variable in curly braces. You may display the contents of the <mark>full_name</mark> variable like so:</p>
		<code>&#123;&#123; $full_name }}</code>
		<p>The code above would return: <strong>{{ $full_name }}</strong></p>
		<br>
		<h3>Echoing Data If It Exists:</h3>
		<p>Sometimes you may wish to echo a variable, but you aren't sure if the variable has been set. However, instead of writing a ternary statement, Slice provides you with the following convenient shortcut, which will be compiled to the ternary statement:</p>
		<code>&#123;&#123; $name or 'Default' }}</code>
		<p>In this example, if the <mark>$name</mark> variable exists, its value will be displayed. However, if it does not exist, the word <strong>Default</strong> will be displayed.</p>
		<p>The code above would return: <strong>{{ $name or 'Default' }}</strong></p>

		<br><hr>

		<h3>Preserving braces:</h3>
		<p>Since many JavaScript frameworks also use "curly" braces to indicate a given expression should be displayed in the browser, you may use the <mark>@</mark> symbol to inform the Slice rendering engine an expression should remain untouched. For example:</p>
		<code>Hello, @&#123;&#123; name }}</code>
		<p>In this example, the @ symbol will be removed by Blade; however, &#123;&#123; name }} expression will remain untouched by the Blade engine, allowing it to instead be rendered by your JavaScript framework.</p>
		<p>The code above would return: <strong>Hello, @{{ name }}</strong></p>

		@yield('content')

		@section('bottom_content')
			<p>This paragraph is the last one!</p>
		@show
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>

</body>
</html>
