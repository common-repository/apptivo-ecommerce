<?php
/**
 * @package 	Apptivo eCommerce
 * @author RajKumar <rmohanasundaram[at]apptivo[dot]com>
 * Custom Products ( Recent Products / Featured Products / Products By category ID and Products By Price )
 */
global $apptivo_ecommerce_loop;
$apptivo_ecommerce_loop['loop'] = 0;

if (!isset($apptivo_ecommerce_loop['columns']) || !$apptivo_ecommerce_loop['columns']) $apptivo_ecommerce_loop['columns'] = apply_filters('loop_products_columns', 4);
?>
<!--  Pagination Top/Both -->
<?php 
if( strtolower(trim($apptivo_ecommerce_loop['pagination_type'])) == '2' || strtolower(trim($apptivo_ecommerce_loop['pagination_type'])) == '3') {
	apptivo_ecommerce_pagination($apptivo_ecommerce_loop['item_tag_url'],'tag',$apptivo_ecommerce_loop['paged']);
	echo '<div class="clear"></div>';
} ?>


<ul class="items" id="item_lists tag">
	<?php 
	
	if ( have_posts()) : while (have_posts()) : the_post(); 
	 $post_id = get_the_ID();
  	$apptivo_ecommerce_loop['loop']++;
		
		?>
		<li class="product <?php if ($apptivo_ecommerce_loop['loop']%$apptivo_ecommerce_loop['columns']==0) echo ' last'; if (($apptivo_ecommerce_loop['loop']-1)%$apptivo_ecommerce_loop['columns']==0) echo ' first'; ?>">
			
			<!-- Item Title and thumbnail Image -->
			<div class="apptivo_product_image">
			<a href="<?php the_permalink(); ?>"><?php echo get_product_thumbnail($post_id); ?></a>
			 </div>
			<!-- Item Sale Price -->
			<?php $sale_price = get_post_meta($post_id, '_apptivo_sale_price', true); ?>
			<span class="price tag">
			      <?php if($sale_price == '') { $sale_price = '0.00'; }?>
				   <?php echo apptivo_ecommerce_price($sale_price);?>				   
			</span>
			<!-- Item Name -->
			<div class="item_name"><a title="<?php echo get_the_title($post_id); ?>" href="<?php echo get_permalink($post_id); ?>"><?php echo apptivo_ecommerce_itemname(trim(get_the_title($post_id)));?></a></div>
			<!--  Add to cart and More details button -->
			<?php
			apptivo_ecommerce_viewdetails_addtocart_btn($post_id,$sale_price);
			?>
			</li>
			<?php 
		
	endwhile; endif;
	/* Products error message */
	if ($apptivo_ecommerce_loop['loop']==0) 
	{
		$products_notfound_message = apply_filters('apptivo_ecommerce_products_error_message','No products found which match your selection');
		echo '<p class="info" style="color:#f00;">'.__($products_notfound_message, 'apptivo_ecommerce').'</p>'; 
	}

	?>

</ul>

<div class="clear"></div>
<!--  Pagination Bottom/Both -->
<?php 
if( strtolower(trim($apptivo_ecommerce_loop['pagination_type'])) == '1' || strtolower(trim($apptivo_ecommerce_loop['pagination_type'])) == '3') {
	apptivo_ecommerce_pagination($apptivo_ecommerce_loop['item_tag_url'],'tag',$apptivo_ecommerce_loop['paged']);
}