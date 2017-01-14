
$(document).ready(function(){
	$("nav#site-navigation").each(function(i, nav){
		$(nav).find("a").each(function(j, a){
			$(a).on("click", function(event){
				//event.preventDefault();
				$(window).scrollTop(document.getElementById(a.hash.substr(1)).offsetTop);
				history.pushState(a.textContent, document.title, window.location.pathname + window.location.search);
				return false;
			});
		});
	});
});


$(document).on("scroll", function(){
	if (window.pageYOffset > 0.5*window.innerHeight){
		$("body.home nav#site-navigation").addClass("dropdown");
	}
	else{
		$("body.home nav#site-navigation").removeClass("dropdown");	
	}
	if (window.pageYOffset > 0.94*window.innerHeight){
		$("body.home nav#site-navigation").addClass("nav-fixed");
	}
	else{
		$("body.home nav#site-navigation").removeClass("nav-fixed");
	}
});