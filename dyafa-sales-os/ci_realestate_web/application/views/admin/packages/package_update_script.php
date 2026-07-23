<script>
		jQuery(document).ready(function($){
						
			$(".child_show_hide").on('change',function(){

				
			
				var child_area = $(this).attr("data-child");
				if ($(this).prop('checked')) {
					$("."+child_area).show();
				}else{
					$("."+child_area).hide();
				}
			});
			$(".hide_child_form").hide();
			

			
			

			$('.dropdown-menu.is_subscription_menus li').click(function() {
				

				var data_val = $(this).find('a').attr('data-val');
				var data_text = $(this).find('a').text();
				$(this).parents('.input-group-btn').find('.dropdown-toggle')
								.html(data_text+'&nbsp;&nbsp;<span class="fa fa-caret-down"></span>')
								.attr('data-btn-val',data_val);
				$(this).parents('.input-group-btn').removeClass('open');
				$(this).parents('.input-group').find('#sub_val_type').val(data_val);
				var combination = $("#sub_validity").val()+' '+$("#sub_val_type").val();
				$("#sub_val_type").val(combination);

				return false;

			});

			$("#sub_validity").on('change',function(){
				//combination = $(this).val()+' '+$("#sub_val_type").val();
				var data_value = $(this).parent().find('button.sub_val_type').attr('data-btn-val');
				$("#sub_val_type").val($(this).val()+' '+ data_value ) ;//$("#sub_val_type").val());
			});

			// pacakge life
			var life;
			$('.dropdown-menu.package_life_menus li').click(function() {

				var data_val = $(this).find('a').attr('data-val');
				var data_text = $(this).find('a').text();
				$(this).parents('.input-group-btn').find('.dropdown-toggle')
					.html(data_text+'&nbsp;&nbsp;<span class="fa fa-caret-down"></span>')
					.attr('data-btn-val',data_val);;
				$(this).parents('.input-group-btn').removeClass('open');
				$(this).parents('.input-group').find('#package_lifetime_val').val(data_val);
				life = $("#package_lifetime").val()+' '+$("#package_lifetime_val").val();
				$("#package_lifetime_val").val(life);
				return false;

				});

				$("#package_lifetime").on('change',function(){
					//life = $(this).val()+' '+$("#package_lifetime_val").val();
					var data_value = $(this).parent().find('button.package_lifetime_val').attr('data-btn-val');
					$("#package_lifetime_val").val($(this).val()+' '+ data_value);
				});
			});
</script>