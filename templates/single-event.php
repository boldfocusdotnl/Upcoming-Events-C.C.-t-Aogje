<?php
/**
 * Template Name: event Page Template
 *
 * A template used to demonstrate how to include the template
 * using this plugin.
 *
 * @package PTE
 * @since 	1.0.0
 * @version	1.0.0
 */
?>

<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap cf">

						<main id="main" class="m-all t-2of3 d-4of7 cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

								<?php 
								// query for retreving al events en order them by date
								$query_args = array(
									'post_type'				=>	'event',
									'posts_per_page'		=>	'-1',
									'post_status'			=>	'publish',
									'ignore_sticky_posts'	=>	true,
									'meta_key'				=>	'event-start-date',
									'orderby' => array('date' => 'DESC', 'meta_value' => 'DESC'),
									// 'orderby'				=>	'meta_value_num',
									'order'					=>	'DESC',
									'meta_query'			=>	$meta_quer_args
								);

								// building the wp query
									$upcoming_events = new WP_Query( $query_args );
									
									 while( $upcoming_events->have_posts() ): $upcoming_events->the_post();
											
											$event_start_date = get_post_meta( get_the_ID(), 'event-start-date', true );
									        $event_start_time = get_post_meta( get_the_ID(), 'event-start-time', true );
									        $event_end_date = get_post_meta( get_the_ID(), 'event-end-date', true );
									        $event_end_time = get_post_meta( get_the_ID(), 'event-end-time', true );
									        $event_venue = get_post_meta( get_the_ID(), 'event-venue', true ); 
									        

									        $event_end = date( 'Ymd', $event_end_date ).date( 'Hi', $event_end_time );
									        $todaysDate = date('YmdHi');

											if( $todaysDate>=$event_end ){
												 // event is past ending o display
											}
											else{
								?>
							    <article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

							    	<header class="article-header">
																<h1 class="page-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
																<!-- <h1 class="page-title"><?php the_title(); ?></h1> -->
									</header>
									<section class="entry-content cf" itemprop="articleBody">
									<?php
										// the content (pretty self explanatory huh)
										the_content();

										/*
										 * Link Pages is used in case you have posts that are set to break into
										 * multiple pages. You can remove this if you don't plan on doing that.
										 *
										 * Also, breaking content up into multiple pages is a horrible experience,
										 * so don't do it. While there are SOME edge cases where this is useful, it's
										 * mostly used for people to get more ad views. It's up to you but if you want
										 * to do it, you're wrong and I hate you. (Ok, I still love you but just not as much)
										 *
										 * http://gizmodo.com/5841121/google-wants-to-help-you-avoid-stupid-annoying-multiple-page-articles
										 *
										*/
										wp_link_pages( array(
											'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'bonestheme' ) . '</span>',
											'after'       => '</div>',
											'link_before' => '<span>',
											'link_after'  => '</span>',
										) );
									?>
									<h6><time class="uep_event_date">Aanvang <?php echo date( 'd F Y', $event_start_date ); ?><?php echo date( ' H: i', $event_start_time); ?> uur en eindigd op <?php echo date( 'd F Y', $event_end_date ); ?> <?php echo date( ' H: i', $event_end_time); ?> uur </time><br/><span class="event_venue">Locatie: <?php echo $event_venue; ?></span></h6>
									</section>
									<footer class="article-footer">
									</footer>
				    <?php 
						}
						//end if
				    endwhile; ?>
				    <?php wp_reset_query();
							
							//if (have_posts()) : while (have_posts()) : the_post(); 
							?>

							<?php //endwhile; else : ?>



							<?php// endif; ?>

						</main>

						<?php get_sidebar(); ?>

				</div>

			</div>


<?php get_footer(); ?>
