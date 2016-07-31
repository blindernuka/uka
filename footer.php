		

		</main><!-- #main -->

<?php
// Previous/next page navigation.
$nav = get_the_posts_pagination(array(
        'prev_text'          => __( '&laquo;', 'uka' ),
        'next_text'          => __( '&raquo;', 'uka' ),
        'screen_reader_text' => __( 'A' ),
		) 
);
if ($nav != null){
	echo '<br>';
	echo '<div class="container row page-navigation-container flex center translucent">';
	$nav = str_replace('<h2 class="screen-reader-text">A</h2>', '', $nav);
	echo $nav;
	echo '</div>';
}
?>
		<br>

		<footer id="colophon" class="site-footer" role="contentinfo">
			<br>
			<div class="container row flex space-around">
			
			<?php if ( get_theme_mod('uka_footer_logo')): ?>
				<img class="column footer-logo logo" src='<?php echo esc_url( get_theme_mod( 'uka_footer_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
			<?php endif; ?>
			
			</div><!-- .container -->
			
			<br>
			
			<?php if ( get_theme_mod('uka_footer_credits')): ?>
				<div class="container row flex space-around">
					<span class="credits"><?php echo get_theme_mod('uka_footer_credits') ?></span>
				</div><!-- .container -->
			<?php endif; ?>
			
			<?php if ( get_theme_mod('uka_footer_copyright')): ?>
				<div class="container row flex space-around">
					<span class="credits"><?php echo '© '.get_theme_mod('uka_footer_copyright').' '.date('Y') ?></span>
				</div><!-- .container -->
			<?php endif; ?>
			
			<br>
			
		</footer><!-- .site-footer -->
</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>