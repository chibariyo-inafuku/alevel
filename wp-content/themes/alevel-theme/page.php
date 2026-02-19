<?php
/*

Template Name: FORM

*/
?>


<?php get_header(); ?>
<div><h1 class="ttl"><?php the_title(); ?></h1></div>
<div class="breadcrumbs"><div class="inner">
<?php if( function_exists( 'aioseo_breadcrumbs' ) ) aioseo_breadcrumbs(); ?>
</div></div>
<div id="contents">
 <div class="page-style">
  


<?php the_content(); ?>
  
  
 </div>

 
</div>
<!-- /contents -->

<?php get_footer(); ?>


<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/js/swiper.js"></script> 
<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/js/script.js"></script> 

<?php wp_footer(); ?>
</body>
</html>
