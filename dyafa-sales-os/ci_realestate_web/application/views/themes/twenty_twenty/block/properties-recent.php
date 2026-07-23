<?php 
	
	/**
	where prop.status = 'publish' order by prop.p_id DESC limit 6
	**/
	
	$def_lang_code = $this->default_language;
	$sql = "select * from properties as prop 
		inner join property_lang_details as pld on pld.p_id = prop.p_id and pld.language = '$def_lang_code'
		inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'	";
		
	$sql = apply_filters("cms_recent_properties_extend_sql" , $sql);


	$where = " where prop.status = 'publish' and prop.deleted = 'N'  ";
	$where = apply_filters("cms_recent_properties_extend_where" , $where);


	$order_by = " order by prop.p_id DESC limit 6 ";
	$order_by = apply_filters("cms_recent_properties_extend_order_by" , $order_by);


	$sql = $sql . $where . $order_by ;	
	
	$recent_properties = $this->Common_model->commonQuery($sql );
	if(isset($recent_properties) && $recent_properties->num_rows() > 0){ ?>
    <div class="site-section site-section-sm bg-light">
      <div class="container">
		
		<div class="row justify-content-center mb-5">
          <div class="col-md-10 text-center">
            <div class="site-section-title  bbb ccc">
              <h2><?php echo mlx_get_lang('Recent Properties'); ?></h2>
            </div>
          </div>
        </div>
		
        <div class="row justify-content-center mb-5">
		
		  
		  <?php foreach($recent_properties->result() as $prop_row){ ?>
			  <div class="col-md-6 col-lg-4 mb-4">
					<?php include(__DIR__ . '../../property/template-part/single-property-grid.php'); ?>
			  </div>
		  <?php } ?>
        </div>
		
		
        <div class="row">
          <div class="col-md-12 text-center">
            <a href="<?php echo site_url('property'); ?>" class="btn custom-btn py-2 px-4 rounded-0 text-white"><?php echo mlx_get_lang('View More'); ?></a>
		  </div>  
        </div>
        
      </div>
    </div>
    <?php } ?>