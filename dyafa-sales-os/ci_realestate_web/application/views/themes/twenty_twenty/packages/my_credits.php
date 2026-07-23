

<div class="content-wrapper">
<section class="content-header">
  <h1 class="page-title"><i class="fa fa-list"></i> <?php echo mlx_get_lang('Front Package Page'); ?>  
  

  <?php if(isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
			}
	?> 
</section>

<section class="content">

  <div class="box box-<?php echo $myHelpers->global_lib->get_skin_class(); ?>">
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
                /*$k = explode('_',$data->meta_key);
                echo  ucwords($k[0].' '.$k[1]);*/
				if($data->meta_key == "subscription_credit")
					echo "Subscription Credit";
				else if($data->meta_key == "post_property_credit")
					echo "Credit For Post Property Posting";
				else if($data->meta_key == "featured_property_credit")
					echo "Credit For Featured Property Posting";
				else if($data->meta_key == "post_blog_credit")
					echo "Credit For Blog Posting";
				
				
               ?></td>
               <td>
               <?php 
			   if($data->meta_key == "subscription_credit")
					echo 'Expires On  '.date("d/m/Y",$data->meta_value); 
				else
					echo $data->meta_value;
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