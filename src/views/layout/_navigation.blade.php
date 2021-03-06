<?

use Illuminate\Support\Facades\Auth;
use Orchestra\Support\Facades\App; ?>

<header class="navbar navbar-fixed-top navbar-inverse" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".main-responsive-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="{{ handles('orchestra::/') }}" class="navbar-brand">
				{{ memorize('site.name', 'Orchestra Platform') }}
			</a>
		</div>
		<div class="collapse navbar-collapse main-responsive-collapse">
			@include('orchestra/foundation::components.menu', array('menu' => App::menu('orchestra')))
			@include('orchestra/foundation::components.usernav')
		</div>
	</div>
</header>

@unless (Auth::check())
<script>
jQuery(function ($) {
	$('a[rel="user-menu"]').on('click', function (e) {
		e.preventDefault();

		window.location.href = "{{ handles('orchestra::login') }}";

		return false;
	});
});
</script>
@endunless
<br>
