;(function($) {

	prettyPrint();

	// normalize nav
	var url   = document.URL;
	var depth = 0;
	// found out our depth
	depth = get_depth(nav, depth);

	var curr = get_current(nav, 0, '');

	var append = '';
	for (var i = 0; i < depth; i++)
	{
		append += '../';
	}

	append_nav(nav, append);

	function append_nav(arr, append)
	{
		// normalize nav
		for (var i = 0; i < arr.length; i++)
		{
			arr[i].uri = append + arr[i].uri;
			if(typeof arr[i].children !== 'undefined')
			{
				append_nav(arr[i].children, append);
			}
		}
	}

	// create dom
	var nav_html  = '<ul>';
	nav_html     += ul_from_array(nav, curr);
	nav_html     += '</ul>';
	$('.vp-docs-menu').first().html(nav_html);

	function ul_from_array(arr)
	{
		var ul = '';
		for (var i = 0; i < arr.length; i++)
		{
			var extra    = '';
			var real_uri = '';
			if(typeof arr[i].children != 'undefined')
			{
				real_uri = arr[i].uri.replace('../', '');
				if(real_uri == curr)
					extra = ' class="selected"';
				ul += '<li '+ extra +'>';
				ul += '<a href="' + arr[i].uri + '.html">' + arr[i].title + '</a>';
				ul += '<ul>';
				ul += ul_from_array(arr[i].children);
				ul += '</ul>';
				ul += '</li>';
			}
			else
			{
				real_uri = arr[i].uri.replace('../', '');
				if(real_uri == curr)
					extra = ' class="selected"';
				ul += '<li '+ extra +'>';
				ul += '<a href="' + arr[i].uri + '.html">' + arr[i].title + '</a>';
				ul += '</li>';
			}
		}
		return ul;
	}

	function get_current(arr, max, curr)
	{
		// normalize nav
		var url   = document.URL;

		// found out our depth
		for (var i = 0; i < arr.length; i++)
		{
			var uri = arr[i].uri;
			if(url.indexOf(uri) !== -1)
			{
				var count = uri.match(/\//g);
				if(!count)
				{
					count = {length: 0};
				}
				if(count.length >= max)
				{
					max  = count.length;
					curr = uri;
				}
			}
			if(typeof arr[i].children !== 'undefined')
			{
				curr = get_current(arr[i].children, max, curr);
			}
		}
		return curr;
	}

	function get_depth(arr, depth)
	{
		// normalize nav
		var url   = document.URL;
		var max   = 0;
		var now   = '';

		// found out our depth
		for (var i = 0; i < arr.length; i++)
		{
			var uri = arr[i].uri;
			if(url.indexOf(uri) !== -1)
			{
				var count = uri.match(/\//g);
				if(count)
				{
					if(count.length > depth)
					{
						depth = count.length;
					}
				}
			}
			if(typeof arr[i].children !== 'undefined')
			{
				depth = get_depth(arr[i].children, depth);
			}
		}
		return depth;
	}

	function fat_collapse(){
		$('a','.fat-accordion').on('click',function(){
			var $container = $(this).closest('.fat-accordion');
			$container.toggleClass('active');
			$('.fat-accordion-content',$container).slideToggle();
		});
	}
	$(document).ready(function(){
		fat_collapse();
	})

}(jQuery));