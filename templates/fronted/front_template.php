<?php if ( ! defined( 'ABSPATH' ) ) exit;   $vcode = $this->_config["vcode"];  ?><script type='text/javascript' language='javascript'> var default_category_id_<?php echo $vcode; ?> = '<?php echo $this->_config["category_id"]; ?>'; <?php echo $this->avpt_js_obj( $this->_config ); ?> </script><?php
	$_avpt_default_date_open = $this->_config["avpt_default_date_open"]; 
	$avpt_category_type = $this->_config["avpt_category_type"]; 
	$post_type = $this->_config["post_type"]; 
	$date_format = $this->_config["date_format"]; 
	$_is_rtl_enable = $this->_config["avpt_enable_rtl"];
	$avpt_enable_post_count = $this->_config["avpt_enable_post_count"];
	$avpt_hide_empty_category = $this->_config["avpt_hide_empty_category"];
	$avpt_default_date_open = $this->_config["avpt_default_date_open"];
	$avpt_short_category_name_by = $this->_config["avpt_short_category_name_by"];
	$avpt_show_all_pane = $this->_config["avpt_show_all_pane"];	
   // $avpt_exclude_category = $this->_config["avpt_exclude_category"]; 
	$avpt_hide_paging = $this->_config["avpt_hide_paging"]; 
	$avpt_hide_post_image = $this->_config["avpt_hide_post_image"]; 
	$avpt_hide_post_short_content = $this->_config["avpt_hide_post_short_content"]; 
	$avpt_select_paging_type = $this->_config["avpt_select_paging_type"]; 
	$avpt_allow_autoclose_accordion = $this->_config["avpt_allow_autoclose_accordion"]; 
	$avpt_hide_post_short_content_length = $this->_config["avpt_hide_post_short_content_length"];  
	$avpt_read_more_link = $this->_config["avpt_read_more_link"]; 
	$_panel_list = $this->getPanelArray($post_type, $date_format);  	
	$avpt_image_content_width = $this->_config["avpt_image_content_width"];	
	$avpt_image_height = $this->_config["avpt_image_height"]; 
	$avpt_shorting_posts_by = $this->_config["avpt_shorting_posts_by"]; 
	$avpt_post_ordering_type = $this->_config["avpt_post_ordering_type"];  
  
	$avpt_space_margin_between_posts = $this->_config["avpt_space_margin_between_posts"];
	$avpt_posts_grid_alignment = $this->_config["avpt_posts_grid_alignment"];
	$avpt_posts_loading_effect_on_pagination = $this->_config["avpt_posts_loading_effect_on_pagination"];
	$avpt_mouse_hover_effect = $this->_config["avpt_mouse_hover_effect"];
	$avpt_show_author_image_and_name = $this->_config["avpt_show_author_image_and_name"]; 
	$template = $this->_config["template"]; 
	 
	if( $avpt_short_category_name_by != "id" ) 
		$avpt_order_category_ids = ""; 

	$_u_agent = $_SERVER['HTTP_USER_AGENT'];
	$_m_browser = '';  
	if(strpos($_u_agent,'MSIE')>-1)
		$_m_browser = 'cls-ie-browser';
		
?> <div id="archivespostaccordion" style="width:<?php echo esc_attr($this->_config["tp_widget_width"]); ?>"  class="  <?php echo (($avpt_allow_autoclose_accordion=="yes")?"avpt_allow_autoclose_accordion":""); ?> <?php echo ((trim($_is_rtl_enable)=="yes")?"avpt-rtl-enabled":""); ?> cls-<?php echo $avpt_posts_grid_alignment; ?>  <?php echo $template; ?> ">
	<?php if($this->_config["hide_widget_title"]=="no"){ ?>
		<div class="ik-panel-tab-title-head" style="background-color:<?php echo esc_attr($this->_config["header_background_color"]); ?>;color:<?php echo esc_attr($this->_config["header_text_color"]); ?>"  >
			<?php echo $this->_config["widget_title"]; ?>   
		</div>
	<?php } ?> 
	<span class='wp-load-icon'>
		<img width="18px" height="18px" src="<?php echo avpt_media.'images/loader.gif'; ?>" />
	</span>
	<div class="wea_content  <?php echo $_m_browser; ?> lt-grid <?php echo esc_attr( $avpt_select_paging_type ); ?> "> 

		<?php 
			 
			$_total_post_count = 0;
			$_category_res_n = array(); 
			
			$_date_range_array = array(); 
				
			if( count( $_panel_list ) > 0 ) {
					 
		        $category_id = $this->_config["category_id"];  
				foreach( $_panel_list as $__pane_key => $__pane_text ) {  
					if( trim($avpt_enable_post_count) == "yes" ) 
						$_count_all_posts = $this->avpt_getTotalPosts( $__pane_key, 0, "", 0, 0 );
					else
						$_count_all_posts = 0;
					
					$_total_post_count = $_count_all_posts + $_total_post_count; 
					$_date_range_array[$__pane_key] = array( "value" => $__pane_text, "count" => $_count_all_posts );
				}
					
			 
				if( trim($avpt_show_all_pane) == "yes" ) { 
				 					
					$arr_category_title = array();
					if( count( $_date_range_array ) > 0 ) {
						foreach( $_date_range_array as $_ckey => $_category_item ) {
							$_category_res_n[$_ckey] = $_category_item;
							$arr_category_title[$_ckey] = array( "value" => $_category_item["value"], "count" => $_category_item["count"] );
						}
					} 
					if($avpt_short_category_name_by=="asc")
						array_multisort($arr_category_title,SORT_ASC,$_category_res_n);
					else
						array_multisort($arr_category_title,SORT_DESC,$_category_res_n);
						
					$_category_res_n = array();
					if( count( $_date_range_array ) > 0 ) {
						$_category_res_n['all'] =   array( "value" => __( 'All', 'archivespostaccordion' ), "count" => $_total_post_count );
						foreach( $_date_range_array as $_ckey => $_category_item ) {
							$_category_res_n[$_ckey] = $_category_item; 
						}
					} 	
						
					$_date_range_array = $_category_res_n;
					
				} 
				
				 
				foreach( $_date_range_array as $__pane_key => $__pane_text ) {
				
				$__pn_item_class = "";
				if( trim( $avpt_default_date_open ) != "" && trim( $avpt_default_date_open ) == $__pane_key ) { 
					$__pn_item_class = " pn-active";
				} 
				
				  ?>
					<div class="item-panel-list">
						<div class="panel-item <?php echo esc_attr( $__pn_item_class ); ?>"  onmouseout="avpt_panel_ms_out( this )" onmouseover="avpt_panel_ms_hover( this )" id="<?php echo esc_attr( $vcode.'-'.$__pane_key ); ?>" onclick="avpt_fillPosts( this.id, '<?php echo esc_attr( $__pane_key );?>', request_obj_<?php echo esc_attr( $vcode ); ?>, 1 )"  style="color:<?php echo esc_attr( $this->_config["panel_text_color"] ); ?>;background-color:<?php echo esc_attr( $this->_config["panel_background_color"] ); ?>;" >
							<div class="panel-item-text"  onmouseout="avpt_panel_ms_out( this.parentNode )" onmouseover="avpt_panel_ms_hover( this.parentNode )">
								<?php echo $__pane_text["value"]; ?> 
								<?php 
									if( trim($avpt_enable_post_count) == "yes" ) 
										echo "(".$__pane_text["count"].")"; 
								?>
							</div>
							<div class="ld-panel-item-text"></div>
							<div class="clr"></div>
						</div>		
						<?php
							$_image_width_item = 0;
							if( intval($avpt_image_content_width) > 0 ) {
								$_image_width_item = intval($avpt_image_content_width); 
							}	 
						?>
						<input type="hidden" class="imgwidth" value = "<?php echo $_image_width_item; ?>" />						
						<div class="item-posts <?php echo $avpt_mouse_hover_effect; ?>">	
							<input type="hidden" class="ikh_templates" value="<?php echo $avpt_posts_grid_alignment; ?>" />
							<input type="hidden" class="ikh_posts_loads_from" value="<?php echo $avpt_posts_loading_effect_on_pagination; ?>" />
							<input type="hidden" class="ikh_border_difference" value="0" />
							<input type="hidden" class="ikh_margin_bottom" value="<?php echo $avpt_space_margin_between_posts; ?>" />
							<input type="hidden" class="ikh_margin_left" value="<?php echo $avpt_space_margin_between_posts; ?>" />
							<input type="hidden" class="ikh_image_height" value="<?php echo $avpt_image_height; ?>" />
							<input type="hidden" class="ikh_item_area_width" value="<?php echo $_image_width_item; ?>" /> 
							<div class="item-posts-wrap">
							<?php 
								 	// Default category opened category start
									if( trim( $avpt_default_date_open ) != "" && trim( $avpt_default_date_open ) == $__pane_key ) { 
								 
										     $_date_format = $avpt_default_date_open;  
											 $post_search_text =  "" ; 
											 $_limit_start = 0;
											 $_limit_end =  $this->_config["number_of_post_display"]; 
											 $is_default_category_with_hidden = 0; 
											 if( $this->_config["hide_categorybox"] == "yes" )
												$is_default_category_with_hidden = 1; 
											 
											?><script language='javascript'><?php echo $this->avpt_js_obj( $this->_config ); ?></script><?php  
											if( $this->avpt_getTotalPosts( $_date_format, 0, $post_search_text, 0, $is_default_category_with_hidden ) > 0 ) {
												$_category_res = $this->getCategories( $category_id, $avpt_category_type, $avpt_hide_empty_category );
												if( count( $_category_res ) > 0 && !( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes' && sanitize_text_field( $this->_config["hide_categorybox"] )=='yes' ) ) {
													?> 
													<div class="ik-post-category" <?php echo (( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes')?"style='padding-top:0'":""); ?> > 
														<?php if( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'no' ) { ?> 
															<input type="text" name="txtSearch" placeholder="<?php echo __( 'Search', 'archivespostaccordion' ); ?>" value="<?php echo esc_html( htmlspecialchars( stripslashes( $post_search_text ) ) ); ?>" class="ik-post-search-text"  /> 
														<?php } ?>
														
														<?php 
														if( sanitize_text_field( $this->_config["hide_categorybox"] ) == 'no' ) { ?>  
																<select name="drpCategory" class="ik-drp-post-category" style="<?php echo (( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes' )?"display:none":""); ?>">
																	<option value="<?php echo $category_id; ?>"><?php echo __('All', 'archivespostaccordion') ?></option>
																	<?php
																		
																		foreach($_category_res as $_category){  
																			
																			$_category_id = "";
																			$_category_name = "";
																			if(isset($_category->id) && !empty($_category->id)) {
																				$_category_id = $_category->id;
																				$_category_name = $_category->category;
																			} else {
																				$_category_id = $_category->term_id;
																				$_category_name = $_category->name;
																			}
									
																		    ?><option value="<?php echo $_category_id; ?>"><?php echo ($this->get_hierarchy_dash($_category->depth)).esc_html( $_category_name ); ?></option><?php
																			 
																		}
																	?>
																</select>  
														<?php } ?>
														
														<span style="<?php echo (( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes' )?"display:none":""); ?>" class="ik-search-button" onclick='avpt_fillPosts( "<?php echo esc_js( $this->_config["vcode"]."-".$_date_format ); ?>", "<?php echo esc_js( $_date_format ); ?>",  request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?>, 2)'> <img width="18px" alt="Search" height="18px" src="<?php echo avpt_media.'images/searchicon.png'; ?>" />
														</span>
														<div class="clrb"></div>
													</div>
												 <?php
												}
											} else { echo "<input type='hidden' value='".$category_id."' class='ik-drp-post-category' />"; }
											$_total_posts = $this->avpt_getTotalPosts( $_date_format, $category_id, $post_search_text, 1, $is_default_category_with_hidden );
											   
											$post_list = $this->getSqlResult( $_date_format, $category_id, $post_search_text, 0, $_limit_end);
											if( $_total_posts > 0 ) { 
											foreach ( $post_list as $_post ) { 
												$image  = $this->getPostImage( $_post->post_image, $avpt_image_content_width, $this->_config["avpt_image_height"] );
												$_author_name = esc_html($_post->display_name);
												$_author_image = get_avatar($_post->post_author,25);
												?>
												<div  style="width:<?php echo esc_attr( $avpt_image_content_width ); ?>px;" class='ikh-post-item-box pid-<?php echo esc_attr( $_post->post_id ); ?>'> 
													<div class="ikh-post-item ikh-simple"> 
														<?php ob_start();
														if( $avpt_hide_post_image == "no" ) { ?> 
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
																	
																		<?php if( sanitize_text_field( $this->_config["hide_post_title"] ) =='no'){ ?> 
																			<a href="<?php echo get_permalink( $_post->post_id ); ?>" style="color:<?php echo esc_attr( $this->_config["title_text_color"] ); ?>" >
																				<?php echo esc_html( $_post->post_name ); ?>
																			</a>	
																		<?php } ?>	 
																		
																		<?php if( sanitize_text_field(  $this->_config["avpt_hide_posted_date"] ) =='no') { ?> 
																				<div class='ik-post-date'>
																					<i><?php echo date( get_option("date_format"), strtotime( $_post->post_date ) ); ?></i>
																				</div>
																		<?php } ?>	
																		
																		<?php if( $avpt_hide_post_short_content == "no" ) { ?>
																			<div class='ik-post-sub-content'>
																				<?php																		
																				 if( strlen( strip_tags( $_post->post_content ) ) > intval( $avpt_hide_post_short_content_length ) ) 	
																					echo substr( strip_tags( $_post->post_content ), 0, $avpt_hide_post_short_content_length )."..";  
																				 else
																					echo trim( strip_tags( $_post->post_content ) );																			
																				?> 
																			</div>
																		<?php } ?>	
																		
																	</div> 
																
																	<?php if( sanitize_text_field(  $this->_config["avpt_hide_comment_count"] ) =='no') { ?> 
																			<div class='ik-post-comment'>
																				<?php 
																					$_total_comments = get_comment_count($_post->post_id); 
																					if($_total_comments["total_comments"] > 0) {
																						echo $_total_comments["total_comments"]; 
																						?> <?php echo (($_total_comments["total_comments"]>1)?__( 'Comments', 'archivespostaccordion' ):__( 'Comment', 'archivespostaccordion' )); 
																					}
																				?>
																			</div>
																	<?php } ?>	
																	
																	<?php if( sanitize_text_field( $this->_config["avpt_show_author_image_and_name"] ) =='yes') { ?> 
																		<div class='ik-post-author'>
																			<?php echo (($_author_image!==FALSE)?$_author_image:"<img src='".avpt_media."images/user-icon.png' width='25' height='25' />"); ?> <?php echo __( 'By', 'archivespostaccordion' ); ?> <?php echo $_author_name; ?>
																		</div>
																	<?php } ?>	
																	
																	<?php if( $avpt_read_more_link == "no" ) { ?>
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
														
														if($avpt_mouse_hover_effect=='ikh-image-style-40'|| $avpt_mouse_hover_effect=='ikh-image-style-41' ){
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
											
											if( $avpt_hide_paging == "no" &&  $avpt_select_paging_type == "load_more_option" && $_total_posts > sanitize_text_field( $this->_config["number_of_post_display"] ) ) { 

													?>
													<div class="clr"></div>
													<div style="display:none" class='ik-post-load-more'  align="center" onclick='avpt_loadMorePosts( "<?php echo esc_js( $_date_format ); ?>", "<?php echo esc_js( $_limit_start+$_limit_end ); ?>", "<?php echo esc_js( $this->_config["vcode"]."-".$_date_format ); ?>", "<?php echo esc_js( $_total_posts ); ?>", request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?> )'>
														<?php echo __('Load More', 'archivespostaccordion' ); ?>
													</div>
													<?php   
												 
											} else if( $avpt_hide_paging == "no" &&  $avpt_select_paging_type == "next_and_previous_links" ) { 

												  ?><div class="clr"></div>
													<div style="display:none" class="avpt-simple-paging"><?php
													echo $this->displayPagination(  0, $_total_posts, $_date_format, $_limit_start, $_limit_end, $this->_config["vcode"], 2 );
													?></div><div class="clr"></div><?php

											} else if( $avpt_hide_paging == "no" &&  $avpt_select_paging_type == "simple_numeric_pagination" ) { 
													?><div class="clr"></div>
													<div style="display:none" class="avpt-simple-paging"><?php
													echo $this->displayPagination(  0, $_total_posts, $_date_format, $_limit_start, $_limit_end, $this->_config["vcode"], 1 );
													?></div><div class="clr"></div><?php
											} else {
												?><div class="clr"></div><?php
											}
										} else {
									
											?><div class="ik-post-no-items"><?php echo __( 'No posts found.', 'archivespostaccordion' ); ?></div><?php 
										
										}		
									
									}  
									// End Default category opened.
							?> 
							</div>
						</div>
						<div class="clr"></div>
					 </div> 
					 <div class="clr"></div>
				   <?php
				}
			}			
		?>
		<div class="clr"></div>
	</div>
</div>
