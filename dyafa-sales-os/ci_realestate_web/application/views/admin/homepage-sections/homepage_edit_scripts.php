<script type="text/javascript">
	
	function update_homepage_dynamic_sections(content)
	{
		
		var row_count = 0;
		
		var count = 1;
		while (count > 0) {
			var cpp = count+1;
			
		   if(!$('.todo-list .section_no_'+count).length && !$('.todo-list .de_'+count).length)
		   {
			   row_count = count;
			   break;
		   }
		   count++;
		}
		
		var prev_class = content.attr('class');
		/*content.attr('class','dynamic_section section_no_'+row_count + ' ' + prev_class);*/
		content.attr('class',' cloned_section section_no_'+row_count + ' ' + prev_class);
		content.find('.minimal').iCheck('destroy');	
		var section = content.attr('data-section');
		if(section!='')
			content.removeClass(section);
		
		
		if(content.find('select').length)
		{
			
			content.find('select').each(function() {
				console.log(' select2 '+$(this).attr("id"));
				$(this).attr("id", $(this).attr("id").replace(/\d+/, row_count));
				$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
			});
		}
		
		
		if(content.find('input[type="text"]').length)
		{
			content.find('input[type="text"]').each(function() {
				$(this).attr("id", $(this).attr("id").replace(/\d+/, row_count));
				$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
			});
		}
		if(content.find('input[type="url"]').length)
		{
			content.find('input[type="url"]').each(function() {
				$(this).attr("id", $(this).attr("id").replace(/\d+/, row_count));
				$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
			});
		}
		if(content.find('input[type="radio"]').length)
		{
			content.find('.radio_toggle_wrapper input[type="radio"]').each(function() {
				$(this).attr("id", $(this).attr("id").replace(/\d+/, row_count));
				$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
			});
		}
		if(content.find('input[type="checkbox"]').length)
		{
			content.find('input[type="checkbox"]').each(function() {
				$(this).attr("id", $(this).attr("id").replace(/\d+/, row_count));
				$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
			});
		}
		if(content.find('input[type="number"]').length)
		{
			content.find('input[type="number"]').each(function() {
				$(this).attr("id", $(this).attr("id").replace(/\d+/, row_count));
				$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
			});
		}
		if(content.find('input[type="hidden"]').length)
		{
			content.find('input[type="hidden"]').each(function() {
				$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
			});
		}
		if(content.find('label').length)
		{
			if(content.find('label').length == 1)
			{
				content.find('label').attr("for", content.find('label').attr("for").replace(/\d+/, row_count));
			}
			else
			{
				content.find('.radio_toggle_wrapper label').each(function() {
					$(this).attr("for", $(this).attr("for").replace(/\d+/, row_count));
				});
				
				content.find('label').each(function() {
					$(this).attr("for", $(this).attr("for").replace(/\d+/, row_count));
				});
			}
			
		}
		
		content.find('.inputtext').each(function(e) {
			$(this).attr("id", $(this).attr("id").replace(/\d+/, row_count));
			$(this).attr("name", $(this).attr("name").replace(/\d+/, row_count));
			
		});
		
		
		content.find('.radio_toggle_wrapper input[type="radio"]:checked').each(function(e) {
			$(this).next('label').trigger('click');
		});
		
		content.find('.minimal').iCheck({
			checkboxClass: 'icheckbox_minimal-blue',
			radioClass: 'iradio_minimal-blue'
		});
		
		
		return content;	
	}
	
	jQuery("document").ready(function($){
		
		/*$('.select2_elem').each(function(e) {
			$(this).select2('destroy');
		});*/
		
		$('.todo-list .select2_elem').each(function(e) {
			$(this).select2({
			  width : '100%'
			});
		});/**/
		
		$(".remove_ds_btn").on('click',function(){
			if(confirm('Do you really want to remove this section?'))
			{
				var thiss = $(this);
				thiss.parents('li').remove();
			}
			return false;
		});
		
		$(".dyna-click").on('click',function(){
			
			var section = $(this).attr("data-section");
			
			var content = $("#dynamic-sections li."+section ).clone(true);
			content = update_homepage_dynamic_sections(content);
			$('.todo-list').append(content);
			return false;
		});
		
		
		
		
		
		
		
		$('.clone_section_btn').on('click',function() {
			var thiss = $(this);
			thiss.tooltip("hide");
			
			
			var content = thiss.parents('li').find('select.select2_elem');
			if(content.length){
				content.each(function() {
					$(this).select2('destroy');
				
				});
			}
			
			var cloned_elem = thiss.parents('li').clone(true);
			cloned_elem = update_homepage_dynamic_sections(cloned_elem);
			
			var new_content = cloned_elem.find('select.select2_elem');
			
			if(new_content.length){
				new_content.each(function() {
					$(this).select2({
							  width : '100%'
							});
				
				});
			}	
			
			thiss.parents('li').after(cloned_elem);
			
			if(content.length){
				content.each(function() {
					$(this).select2({
							  width : '100%'
							});
				
				});
			}
			
			
			
			
			return false;
		});
		
	});

</script>