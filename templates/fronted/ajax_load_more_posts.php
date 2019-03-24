<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly    
	$params = $_REQUEST;  
	$_date_format = ( isset( $params["date_format"] ) ? ( $params["date_format"] ) : 0 ); 
	$_total = ( isset( $params["total"] ) ? intval( $params["total"] ) : 0 );
	$category_id = ( isset( $params["category_id"] ) ?  ( $params["category_id"] ) : 0 );
	$post_search_text = ( isset( $params["post_search_text"] ) ? esc_html( $params["post_search_text"] ) : "" ); 
	$_limit_start =( isset( $params["limit_start"] ) ? intval( $params["limit_start"] ) : 0 );
	$_limit_end = intval( $params["number_of_post_display"] ); 
	$all_pg = ceil( $_total / intval( $params["number_of_post_display"] ) );
	$cur_all_pg =ceil( ( $_limit_start ) / intval( $params["number_of_post_display"] ) );  
	$final_width = $params["avpt_image_content_width"];  
	$avpt_image_content_width = $final_width;
	$avpt_image_height = ( $params["avpt_image_height"] ); 
	$avpt_mouse_hover_effect = $params["avpt_mouse_hover_effect"];
    $avpt_default_category = ( ( isset( $params["avpt_default_category"] ) && trim($params["avpt_default_category"]) != "0") ?  ( $params["avpt_default_category"] ) : $category_id );	
	
	 
	$_total_posts = $this->avpt_getTotalPosts( $_date_format, $avpt_default_category, $post_search_text, 1, 0 );
	if( $_total_posts <= 0 ) {
		?><div class="ik-post-no-items"><?php echo __( 'No posts found.', 'archivespostaccordion' ); ?></div><?php
		die();
	}
	foreach( $_result_items as $_post ) {
	  
		$image = $this->getPostImage( $_post->post_image, $final_width, $params["avpt_image_height"] );
		$_author_name = esc_html($_post->display_name);
		$_author_image = get_avatar($_post->post_author,25);
		?>
		<div style="width:<?php echo esc_attr($final_width); ?>px; " class='ikh-post-item-box pid-<?php echo esc_attr( $_post->post_id ); ?>'> 
			<div class="ikh-post-item ikh-simple">  
			<?php
			ob_start();
			if( $params["avpt_hide_post_image"] == "no" ) { ?>
			    <div  class='ikh-image' > 
					<a href="<?php echo get_permalink( $_post->post_id ); ?>">	 
						<?php echo $image; ?>
					</a>  
				</div>
			 <?php }  
			  $_ob_image = ob_get_clean(); 
					 
			  ob_start();
			  ?> 
				<div class='ikh-content'>
					 <div class="ikh-content-data">
					
						<div class='ik-post-name'>  
							<?php if(sanitize_text_field( $params["hide_post_title"] )=='no'){ ?> 
								 <a href="<?php echo get_permalink($_post->post_id); ?>" style="color:<?php echo esc_attr( $params["title_text_color"] ); ?>" >
										<?php echo esc_html( $_post->post_name ); ?>
								 </a>	
							<?php } ?>	  
							
							<?php if( sanitize_text_field( $params["avpt_hide_posted_date"] ) =='no'){ ?> 
									<div class='ik-post-date'>
										 <i><?php echo date(get_option("date_format"),strtotime($_post->post_date)); ?></i>
									</div>
							<?php } ?>	
						
							<?php if( $params["avpt_hide_post_short_content"] == "no" ) { ?>
									<div class='ik-post-sub-content'>
										<?php
										if( strlen( strip_tags( $_post->post_content ) ) > intval( $params["avpt_hide_post_short_content_length"] ) ) 	
											echo substr( strip_tags( $_post->post_content ), 0, $params["avpt_hide_post_short_content_length"] ).".."; 
										else
											echo trim( strip_tags( $_post->post_content ) );
										?> 
									</div>
							<?php } ?>	
						</div> 
					
						<?php if( sanitize_text_field( $params["avpt_hide_comment_count"] ) =='no'){ ?> 
								<div class='ik-post-comment'>
									<?php 
										$_total_comments = (get_comment_count($_post->post_id)); 
										if($_total_comments["total_comments"] > 0) {
											echo $_total_comments["total_comments"]; 
											?> <?php echo (($_total_comments["total_comments"]>1)?__( 'Comments', 'archivespostaccordion' ):__( 'Comment', 'archivespostaccordion' )); 
										}
									?>
								</div>
						<?php } ?>
						
						<?php if( sanitize_text_field( $params["avpt_show_author_image_and_name"] ) =='yes') { ?> 
							<div class='ik-post-author'>
								<?php echo (($_author_image!==FALSE)?$_author_image:"<img src='".avpt_media."images/user-icon.png' width='25' height='25' />"); ?> <?php echo __( 'By', 'archivespostaccordion' ); ?> <?php echo $_author_name; ?>
							</div>
						<?php } ?>	

						<?php if( $params["avpt_read_more_link"] == "no" ) { ?>	
							<div class="avpt-read-more-link">
								<a class="lnk-post-content" href="<?php echo get_permalink( $_post->post_id ); ?>" >
									<?php echo __( 'Read More', 'archivespostaccordion' ); ?>
								</a>
							</div>	
						<?php } ?> 
					</div>		
			 </div>	
			<?php  
			$_ob_content = ob_get_clean(); 
													
			if($avpt_mouse_hover_effect=='ikh-image-style-40' || $avpt_mouse_hover_effect=='ikh-image-style-41' ){
				echo $_ob_content;
				echo $_ob_image;
			} else {
				echo $_ob_image;
				echo $_ob_content;														
			}	
			?> 
			<div class="clr1"></div>
		  </div>
		</div> 
		<?php 
	}  
	if( $params["avpt_hide_paging"] == "no" && $params["avpt_select_paging_type"] == "load_more_option"  && ( $all_pg ) >= $cur_all_pg + 2 ) {
			
			?><div class="clr"></div>			
			<?php if( $params["avpt_select_paging_type"] == "load_more_option" ) { ?>		
				<div style="display:none" class='ik-post-load-more' align="center" onclick='avpt_loadMorePosts("<?php echo esc_js( $_date_format ); ?>","<?php echo esc_js( $_limit_start+$_limit_end ); ?>","<?php echo esc_js( $params["vcode"]."-".$_date_format ); ?>","<?php echo esc_js( $_total ); ?>",request_obj_<?php echo esc_js( $params["vcode"] ); ?>)'>
					<?php echo __( 'Load More', 'archivespostaccordion' ); ?>
				</div>
			<?php }  
			
	} else if( $params["avpt_hide_paging"] == "no" && $params["avpt_select_paging_type"] == "next_and_previous_links" ) {
	
			?><div class="clr"></div>
			<div style="display:none" class="avpt-simple-paging"><?php
			echo $this->displayPagination( $cur_all_pg, $_total, esc_js( $_date_format ), $_limit_start, $_limit_end, $params["vcode"], 2 );
		  ?></div><div class="clr"></div><?php
		 
	} else if( $params["avpt_hide_paging"] == "no" && $params["avpt_select_paging_type"] == "simple_numeric_pagination" ) {	
		?><div class="clr"></div>
			<div style="display:none" class="avpt-simple-paging"><?php
			echo $this->displayPagination( $cur_all_pg, $_total, esc_js( $_date_format ), $_limit_start, $_limit_end, $params["vcode"], 1 );
		  ?></div><div class="clr"></div><?php
	} else {
		?><div class="clr"></div><?php
	}
	?><script type='text/javascript' language='javascript'><?php echo $this->avpt_js_obj( $params ); ?></script>
	