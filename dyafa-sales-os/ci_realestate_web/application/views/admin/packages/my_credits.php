

<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-list"></i> <?php echo mlx_get_lang('Front Package Page'); ?>  
  

  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
			
			$package_features_arr = array();
			if(count($package_features_add)){
				foreach($package_features_add as $pfkey => $pck_features){
					if(isset($pck_features['features'])){
						foreach($pck_features['features'] as $feature){	
							$package_features_arr [$feature['feature_type']] = $feature;
						}	
					}
				}
			}
	?> 
</section>

<section class="content">

  <div class="box box-<?php echo get_skin_class(); ?>">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo mlx_get_lang('My Credits'); ?></h3>
	</div>
	<div class="box-body content-box">
        <?php if(isset($query) && ($query->num_rows() > 0)){ ?>    
        <table class="table table-bordered">
        <tr>
        <th><?php echo mlx_get_lang('For'); ?></th>
        <th><?php echo mlx_get_lang('Credits'); ?></th>
        </tr>     
		  <?php foreach ($query->result() as $data) {
				
			  ?>
            <tr>
            <td><?php 
                
				$cdata = ['credit' => $data->meta_key , 'credit_value' => $data->meta_value];
				if(!apply_filters("cms_show_credit_title",$cdata)){
					
					echo "CreditSSSS for ".$data->meta_key;
				}
				
               ?></td>
               <td>
               <?php 
			   
			   if(!apply_filters("cms_show_credit_value",$cdata)){
					
					echo "CreditSSSS for ".$data->meta_key;
				}
				
			   
				?>
               </td>
            </tr>      
          <?php  }
          }else{
              echo mlx_get_lang('You Have 0 Credit Buy Now');
            }?>
            </table>
        
		</div>

                   
  </div>
</section>
</div>