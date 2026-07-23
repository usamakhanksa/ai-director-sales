<style>
.general-stats-block .overlay{
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	line-height: 115px;
	background-color:rgba(255,255,255,0.3);
}
.general-stats-block .overlay i{
	font-size: 28px;
}
</style>
<div class="row general-stats-block">
	<div class="col-lg-3 col-xs-6 text-center">
	  <div class="small-box bg-aqua">
		<div class="inner">
		  <h3 id="ga_visitors">-</h3>
		  <p>Visitors</p>
		</div>
		<div class="icon">
		  <i class="ion ion-bag"></i>
		</div>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
	  </div>
	  </div>
	  
	</div>
	
	<div class="col-lg-3 col-xs-6 text-center">
	 <div class="small-box bg-blue">
		<div class="inner">
		  <h3 id="ga_new_visitors">-</h3>
		  <p>New Visitors</p>
		</div>
		<div class="icon">
		  <i class="ion ion-pie-graph"></i>
		</div>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
	  </div>
	 </div>
	</div>
	
	<div class="col-lg-3 col-xs-6 text-center">
	  <div class="small-box bg-purple">
		<div class="inner">
		  <h3 id="ga_sessions">-</h3>
		  <p>Sessions</p>
		</div>
		<div class="icon">
		  <i class="ion ion-bag"></i>
		</div>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
	  </div>
	  </div>
	</div>
	
	
	<div class="col-lg-3 col-xs-6 text-center">
	  <div class="small-box bg-navy">
		<div class="inner">
		  <h3 id="ga_page_views">-</h3>
		  <p>Page Views</p>
		</div>
		<div class="icon">
		  <i class="ion ion-bag"></i>
		</div>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
	  </div>
	  </div>
	</div>
	
	<div class="col-lg-3 col-xs-6 text-center">
	  <div class="small-box bg-red">
		<div class="inner">
		  <h3 id="ga_pageviewspersession">-</h3>
		  <p>Pages/Session</p>
		</div>
		<div class="icon">
		  <i class="ion ion-pie-graph"></i>
		</div>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
	  </div>
	  </div>
	</div>
	
	
	
	<div class="col-lg-3 col-xs-6 text-center">
	  <div class="small-box bg-yellow ">
		<div class="inner">
		  <h3 id="ga_session_per_user">-</h3>
		  <p>Sessions Per User</p>
		</div>
		<div class="icon">
		  <i class="ion ion-person-add"></i>
		</div>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
	  </div>
	  </div>
	</div>
	
	<div class="col-lg-3 col-xs-6 text-center">
	  <div class="small-box bg-orange">
		<div class="inner">
		  <h3 id="ga_avg_session_duration">-</h3>
		  <p>Avg. Session Duration</p>
		</div>
		<div class="icon">
		  <i class="ion ion-pie-graph"></i>
		</div>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
	  </div>
	  </div>
	</div>
	
	
	<div class="col-lg-3 col-xs-6 text-center">
	  <div class="small-box bg-green">
		<div class="inner">
		  <h3 id="ga_bounce_rate">-</h3>
		  <p>Bounce Rate</p>
		</div>
		<div class="icon">
		  <i class="ion ion-stats-bars"></i>
		</div>
		<div class="overlay">
		  <i class="fa fa-refresh fa-spin"></i>
	  </div>
	  </div>
	</div>
</div>


<script>
var hitsData = [];
$(document).ready(function(){
	$.ajax({url: "<?php echo base_url().'admin/ajax_ga/show_ga_data';?>", success: function(data)
	{
		if(data != null){
			var result = JSON.parse(data);
			Object.keys(result).forEach(function(key) {
				$("#ga_"+key+"").html(result[key]);
				$("#ga_"+key+"").parents('.small-box').find('.overlay').hide();
			});
		}
	}
	});	
});	
</script>