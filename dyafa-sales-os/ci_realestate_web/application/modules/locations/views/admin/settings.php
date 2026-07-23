
      <?php $this->load->view("admin/header-top");?>
      
	  <?php $this->load->view("admin/sidebar-left");?>
      

<?php 
	/*if(isset($options_list) && $options_list->num_rows()>0)
	{
		
		foreach($options_list->result() as $row)
		{
			${$row->option_key} = $row->option_value;
		}
	}*/
?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1> Document Settings </h1>
          <?php 
			if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
			
			
			
			?>
        </section>

        <!-- Main content -->
        <section class="content">
			<!-- form start -->
               <!-- <form role="form">-->
             <?php 
			$attributes = array('name' => 'add_form_post','class' => 'form');		 			
			echo form_open_multipart('admin/locations/settings',$attributes); 
			
			?>
			<input type="hidden" name="user_id" class="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">	
			<div class="row">
			<div class="col-md-8">   
			   
			<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Document Settings</h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
                </div><!-- /.box-header -->
				
				
                  <div class="box-body">
                    
					 <div class="form-group">
                      <label>Location title </label>
                      <input name="loc_title" type="text" class="form-control" placeholder="">
					  <p> i.e. Country or State or City or Zipcode </p>
                    </div>
					
					<div class="form-group">
                      <label>Parent of current Location</label>
                      <select name="loc_parent" class="form-control">
                        <option value="0">Root or Master Location</option>
                      </select>
                    </div>
					
					
					
                  </div>

              </div>
			  
			  
		  </div>
		  
		  <div class="col-md-4">
		  <div class="box box-primary">
			  <div class="box-header with-border">
                  <h3 class="box-title"> Status</h3>
				  <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				  </div>
                </div>
				 
			  	 <div class="box-footer">
					<button name="submit" type="submit" class="btn btn-primary pull-right" id="save_publish">Save Changes</button>
                  </div>
			  </div>
		  </div>
		  
		  
		  </div>
		  
			</form>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      