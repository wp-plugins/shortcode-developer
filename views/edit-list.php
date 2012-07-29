<?php

/*
*  Edit List
*
*  @description: 
*  @created: 27/07/12
*/

$shortcodes = $this->get_shortcodes();

?>
<div class="wrap">
	
	<div class="icon32" id="icon-options-general"><br></div>
	<h2><?php _e("Shortcodes", 'scd'); ?></h2>
	
	<?php $this->display_message(); ?>
	
	<div id="shortcode-developer" class="clearfix">
		
		<!-- Side Column -->
		<div class="col-side">
			
			<div class="wp-box">
				<div class="inner">
					<h3 class="h2"><?php _e("Shortcode Developer", 'scd'); ?></h3>
					<h3>v<?php echo $this->version; ?></h3>
					<p><?php _e("Welcome to Shortcode Developer. Here you can quickly create and edit shortcodes to use within your website.",'le'); ?></p>
					<p><?php _e("This plugin gives you full PHP control whilst taking care of the hard work in the background!",'scd'); ?></p>
					<p><?php _e("Creating shortcodes has never been this easy!",'scd'); ?></p>

				</div>
				<div class="footer">
					<ul class="left hl">
						<li><?php _e("Created by", 'scd'); ?> Elliot Condon</li>
					</ul>
					<ul class="right hl">
						<li><a href="http://wordpress.org/extend/plugins/shortcode-developer/"><?php _e("Vote", 'scd'); ?></a></li>
						<li><a href="http://twitter.com/elliotcondon"><?php _e("Follow", 'scd'); ?></a></li>
					</ul>
				</div>
			</div>
			
		</div>
		<!-- / Side Column -->
		
		<!-- Main Column -->
		<div class="col-main">
			<div class="float-wrap">
			
				<!-- Shortcode List -->
				<div id="shortcode-list" class="wp-box">
				
<table class="widefat scd-widefat">
	<thead>
		<tr>
			<th style="width:33%;"><?php _e("Name",'scd'); ?></th>
			<th style="width:33%;"><?php _e("Shortcode",'scd'); ?></th>
			<th><?php _e("Parameters",'scd'); ?></th>
		</tr>
	</thead>
	
	<tbody>
		<?php if( $shortcodes ): ?>
			<?php foreach( $shortcodes as $shortcode ): ?>
			<tr>
				<td>
					<strong>
						<a title="Edit <?php echo $shortcode['name']; ?>" href="<?php echo $this->data['url']; ?>&action=edit&id=<?php echo $shortcode['id']; ?>" class="row-title"><?php echo $shortcode['name']; ?></a>
					</strong>
					<div class="row-actions">
						<span class="edit">
							<a title="<?php _e("Edit this shortcode",'scd'); ?>" href="<?php echo $this->data['url']; ?>&action=edit&id=<?php echo $shortcode['id']; ?>"><?php _e("Edit",'scd'); ?></a>  
						</span>
						|
						<span class="trash">
							<a title="<?php _e("Delete this shortcode",'scd'); ?>" href="<?php echo $this->data['url']; ?>&action=delete&id=<?php echo $shortcode['id']; ?>" class="delete-shortcode"><?php _e("Delete",'scd'); ?></a>
						</span>
					</div>
				</td>
				<td>
				<?php
				
				// create shortcode string
				$html = '[' . $shortcode['name'] . '{atts}]';
				
				if( strpos($shortcode['php'], '$content') !== false)
				{
					// found $content in PHP
					$html .= ' ... [' . $shortcode['name'] . ']';
				}
				
				echo str_replace('{atts}', '', $html);
				
				?>
				</td>
				<td>
				<?php
				
				// create shortcode string
				$atts = "";
				if( $shortcode['atts'] )
				{
					foreach( $shortcode['atts'] as $att )
					{
						$atts .= ' ' . $att['name'] . '="' . $att['default'] . '"';
					}
				}
				
				echo str_replace('{atts}', $atts, $html);
				
				?>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="3">
					<?php _e("No shortcodes. Click the <strong>Add Shortcode</strong> button to get started.",'scd'); ?>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
					
					<div class="list-footer">
						<a class="scd-button" id="add-shortcode" href="<?php echo $this->data['url']; ?>&action=edit"><?php _e("Add Shortcode",'scd'); ?></a>
					</div>
				
				</div>
				<!-- / Shortcode List -->

			</div>
		</div>
		<!-- / Main Column -->
		
	</div>

</div>