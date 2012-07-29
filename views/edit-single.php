<?php

/*
*  Edit Single
*
*  @description: This template is used to edit or add a new shortcode
*  @created: 27/07/12
*/

$shortcode = array(
	'id' => false,
	'name' => '',
	'atts' => array(),
	'php' => ''
);


// load shortcode
if( $this->params['id'] )
{
	$shortcode = $this->get_shortcode( $this->params['id'] );
}


/*
<!-- Breadcrumbs -->
<div class="breadcrumbs">
	<ul class="hl clearfix">
		<li><a href="<?php echo $this->data['url']; ?>">&laquo; Back to list</a></li>
	</ul>
</div>
<!-- / Breadcrumbs -->*/
?>
<form method="post" action="">

<!-- Hidden Inputs -->
<div style="display:none;">
	<input type="hidden" name="id" value="<?php echo $shortcode['id']; ?>" />
	<input type="hidden" name="atts" value="" />
</div>
<!-- / Hidden Inputs -->

<div class="wrap">
	
	<div class="icon32" id="icon-options-general"><br></div>
	<h2><?php if( ! $shortcode['id'] )
		{
			_e("Create New Shortcode", 'scd');
		}
		else
		{
			_e("Edit Shortcode", 'scd');
		}
		?></h2>
	
	<?php $this->display_message(); ?>
	
	<div id="shortcode-developer" class="clearfix">
		
		<!-- Side Column -->
		<div class="col-side">
			
			<div class="wp-box">
				<div class="header">
					<h3><?php _e("Publish",'scd'); ?></h3>
				</div>
				<div class="inner">
					
					<?php if( $shortcode['id'] ): ?>
						<p><a href="<?php echo $this->data['url']; ?>&action=delete&id=<?php echo $shortcode['id']; ?>" class="delete-shortcode"><?php _e("Delete Shortcode",'scd'); ?></a></p>
					<?php endif; ?>
					
					<input type="submit" value="<?php _e("Save",'scd'); ?>" class="scd-button" id="publish" name="publish" />
					
				</div>
			</div>
			
		</div>
		<!-- / Side Column -->
		
		<!-- Main Column -->
		<div class="col-main">
			<div class="float-wrap">
				
				<!-- shortcode-single -->
				<div class="wp-box" id="shortcode-single">
					<table class="scd-form">
						<tr>
							<th class="side-th">
								<label><span class="required">*</span><?php _e("Shortcode Name",'scd'); ?></label>
								<p><?php _e('Single word, lower case. <br />
								eg: "message" or "subscribe_button"','scd'); ?></p>
							</th>
							<td class="main-td">
								<input type="text" name="name" class="label" id="form-name" value="<?php echo $shortcode['name']; ?>">
							</td>
						</tr>
						<tr>
							<th class="side-th">
								<label><span class="required"></span><?php _e("Shortcode Attributes",'scd'); ?></label>
								<p><?php _e("Attributes are used to pass values from the shortcode to the executable PHP function.",'scd'); ?></p>
								<p><?php _e("All attributes are of type string.",'scd'); ?></p>
							</th>
							<td class="main-td">
								
								<div id="shortcode-atts" class="wp-box">
									
									<script type="text/html" id="shortcode-atts-html">
										<tr>
											<td>
												<input class="name" type="text" name="atts[999][name]" value="" />
											</td>
											<td>
												<a class="delete-attribute" href="#">x</a>
												<input class="default" type="text" name="atts[999][default]" value="" />
											</td>
										</tr>
									</script>
									
									<table class="widefat scd-widefat">
										<thead>
											<tr>
												<th style="width:50%;"><?php _e("Name",'scd'); ?></th>
												<th><?php _e("Default Value",'scd'); ?></th>
											</tr>
										</thead>
										<tbody>
										<?php if( $shortcode['atts'] ): foreach( $shortcode['atts'] as $i => $att ): ?>
											<tr>
												<td>
													<input class="name" type="text" name="atts[<?php echo $i; ?>][name]" value="<?php echo $att['name']; ?>" />
												</td>
												<td>
													<a class="delete-attribute" href="#">x</a>
													<input class="default" type="text" name="atts[<?php echo $i; ?>][default]" value="<?php echo $att['default']; ?>" />
												</td>
											</tr>
										<?php endforeach; endif; ?>
										</tbody>
									</table>
									
									<div class="list-empty-message" <?php if( $shortcode['atts'] ){ echo 'style="display:none"'; } ?>>
										<?php _e("No Attributes",'scd'); ?>
									</div>

									<div class="list-footer">
										<a class="scd-button" id="add-shortcode-attr" href="#"><?php _e("Add Attribute",'scd'); ?></a>
									</div>
								</div>
								
							</td>
						</tr>
						<tr>
							<th class="side-th">
								<label><span class="required">*</span><?php _e("PHP",'scd'); ?></label>
								<p><?php _e("Create the executable PHP function to return HTML to your shortcode placeholder",'scd'); ?></p>
								<p><?php _e('Do not echo any html. Instead, add it to the $html variable','scd'); ?></p>
							</th>
							<td class="main-td">
<div id="form-php-vars-wrapper">
<textarea id="form-php-vars">&lt;?php

/*
*	<?php _e("Available Variables",'scd'); ?> 
*
*	$content: <?php _e("Text entered between the shortcode open / close tags. Defaults to null",'scd'); ?> 
*/

$html = "";</textarea>
</div>

<div id="form-php-body-wrapper">
<textarea name="php" id="form-php-body"><?php echo $shortcode['php']; ?></textarea>
</div>

<div id="form-php-return-wrapper">
<textarea id="form-php-return">return $html;

?&gt;</textarea>
</div>
							</td>
						</tr>
						<tr>
							<th class="side-th"><?php _e("Usage",'scd'); ?></th>
							<td class="main-td">
								<?php if( $shortcode['id'] ): 
																
									// create shortcode string
									$html = '[' . $shortcode['name'] . '{atts}]';
									
									if( strpos($shortcode['php'], '$content') !== false)
									{
										// found $content in PHP
										$html .= ' ... [' . $shortcode['name'] . ']';
									}
									
									$atts = "";
									if( $shortcode['atts'] )
									{
										foreach( $shortcode['atts'] as $att )
										{
											$atts .= ' ' . $att['name'] . '="' . $att['default'] . '"';
										}
									}
									
									echo str_replace('{atts}', $atts, $html);
									
								else: ?>
									<?php _e("Available on save.",'scd'); ?>
								<?php endif; ?>
							</td>
						</tr>
					</table>
				</div>
				<!-- / shortcode-single -->
			
			</div>
		</div>
		<!-- / Main Column -->
		
	</div>
</div>

</form>