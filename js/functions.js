/*
*  ShortCode
*
*  @description: 
*  @created: 27/07/12
*/

var shortcode = {
	text : {
		'confirm_delete' : "Are you sure?",
		'form_php_vars' : '',
		'form_php_var' : "${name}: Defaults to \"{default}\""
	},
	codemirror : {
		editor_vars : null,
		editor_body : null,
		editor_return : null
	}
};

		
(function($){

	/*
	*  Exists
	*  
	*  @since			3.1.6
	*  @description		returns true or false on a element's existance
	*/
	
	$.fn.exists = function()
	{
		return $(this).length>0;
	};
	
	
	/*
	*  uniqid
	*  
	*  @since			3.1.6
	*  @description		Returns a unique ID (secconds of time)
	*/
	
	function uniqid()
    {
    	var newDate = new Date;
    	return newDate.getTime();
    }
	
	
	/*
	*  Place Confirm message on Publish trash button
	*  
	*  @since			3.1.6
	*  @description		
	*/
	
	$('.delete-shortcode').live('click', function(){
			
		var response = confirm( shortcode.text.confirm_delete );
		if(!response)
		{
			return false;
		}
		
	});
	
	
	/*
	*  Delete Attribute
	*
	*  @description: 
	*  @created: 28/07/12
	*/
	
	$('#shortcode-atts .delete-attribute').live('click', function(){
		
		// vars
		var tr = $(this).closest('tr'),
			siblings = tr.siblings('tr').length;
			

		tr.remove();
			
			
		if( siblings == 0 )
		{
			$('#shortcode-atts .list-empty-message').show();
		}
		
		
		update_available_variables();
			
	
		return false;
		
	});
	
	
	/*
	*  Short Code Name Validation
	*
	*  @description: 
	*  @created: 28/07/12
	*/
	$('#form-name, #shortcode-atts .name').live('change', function(){
		
		var val = $(this).val();
		
		val = val.replace(' ', '_');
		val = val.toLowerCase();
		
		$(this).val(val);
			
	});
	
	
	/*
	*  Add Attribute
	*
	*  @description: 
	*  @created: 27/07/12
	*/
	
	$('#add-shortcode-attr').live('click', function(){
		
		// vars
		var atts = $('#shortcode-atts'),
			tbody = atts.find('table tbody'),
			empty_message = atts.find('.list-empty-message'),
			tmpl = atts.find('#shortcode-atts-html').html(),
			count = tbody.children().length;
		

		// replace [999]
		tmpl = tmpl.replace(/\[999]/g, '[' + count + ']');
		
		
		// hide empty message
		empty_message.hide();
		
		
		// add tr
		tbody.append( tmpl );
		
		
		// return false
		return false;
		
		
	});
	
	
	/*
	*  Update Available Variables
	*
	*  @description: 
	*  @created: 27/07/12
	*/
	
	$('#shortcode-atts input[type="text"]').live('change', function(){
		
		update_available_variables();
		
	});
	
	function update_available_variables()
	{
		// vars
		var atts = $('#shortcode-atts'),
			php_vars = $('#form-php-vars'),
			html = "";
		
		
		atts.find('tbody tr').each(function(){
			
			// vars
			var name = $(this).find('.name').val(),
				default_value = $(this).find('.default').val(),
				text = shortcode.text.form_php_var;
			
			
			// replace vals
			text = text.replace('{name}', name);
			text = text.replace('{default}', default_value);
			
			html += "*\t" + text + "\n";
			
		});
		
		
		// add end of html
		html += "*/";
		
		
		// add to textarea
		shortcode.codemirror.editor_vars.setValue( shortcode.text.form_php_vars.replace('*/', html) );
	}
	
	
	/*
	*  Document Ready
	*
	*  @description: 
	*  @created: 27/07/12
	*/
	
	$(document).ready( function(){
		
		// vars
		var php_vars = $('#form-php-vars'),
			php_body = $('#form-php-body'),
			php_return = $('#form-php-return');
		
		
		// set php_vars as a text variable
		shortcode.text.form_php_vars = php_vars.val();
		
		
		// Create CodeMirror on PHP vars
		shortcode.codemirror.editor_vars = CodeMirror.fromTextArea( php_vars.get(0) , {
			theme : "default",
			lineNumbers: true,
	        matchBrackets: true,
	        mode: "application/x-httpd-php",
	        indentUnit: 4,
	        indentWithTabs: true,
	        enterMode: "keep",
	        tabMode: "shift",
	        readOnly : true
		});
		
		
		// Create CodeMirror on PHP body
		shortcode.codemirror.editor_body = CodeMirror.fromTextArea( php_body.get(0) , {
			theme : "default",
			lineNumbers: true,
	        matchBrackets: true,
	        mode: "application/x-httpd-php-open",
	        indentUnit: 4,
	        indentWithTabs: true,
	        enterMode: "keep",
	        tabMode: "shift"
		});
		
		// Create CodeMirror on PHP return
		shortcode.codemirror.editor_return = CodeMirror.fromTextArea( php_return.get(0) , {
			theme : "default",
			lineNumbers: true,
	        matchBrackets: true,
	        mode: "application/x-httpd-php-open",
	        indentUnit: 4,
	        indentWithTabs: true,
	        enterMode: "keep",
	        tabMode: "shift",
	        readOnly : true
		});
		
		
		// update available variables
		update_available_variables();
		
		
	});

})(jQuery);