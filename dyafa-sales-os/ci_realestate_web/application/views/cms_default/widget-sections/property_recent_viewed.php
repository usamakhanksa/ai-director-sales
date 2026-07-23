<?php


$recentlyViewed = $this->session->userdata('recentlyViewed');
if (!is_array($recentlyViewed)) {
    $recentlyViewed = array();
}
    //change this to 10
    /*if(sizeof($recentlyViewed)>3){
        array_shift($recentlyViewed);
    }*/
    //here set your id or page or whatever
    /*if(!in_array($single_property->p_id,$recentlyViewed)){
        array_push($recentlyViewed,$single_property->p_id);
    }*/

    /*print_r($single_property)*/;

if (!array_key_exists($single_property->p_id, $recentlyViewed)) {

    $recentlyViewed[$single_property->p_id] = array(
        'pid' => $single_property->p_id,
        'title' => $single_property->title,
        'url' => current_url()
    );
    //array_push($recentlyViewed,$single_property->p_id);
    //print_r($recentlyViewed);
} else {
    //unset($recentlyViewed [$single_property->p_id]);
    $recentlyViewed[$single_property->p_id] = array(
        'pid' => $single_property->p_id,
        'title' => $single_property->title,
        'url' => current_url()
    );
}

$this->session->set_userdata('recentlyViewed', $recentlyViewed);

if (count($recentlyViewed) > 0 && $this->enable_multi_lang) {
    foreach ($recentlyViewed as $kk => $vv) {
        if ($single_property->p_id == $kk) {
            unset($recentlyViewed[$kk]);
            continue;
        }
        $sql = "select prop.p_id
					   from properties  as prop 
			   inner join users as u on u.user_id = prop.created_by and u.user_status = 'Y'
			   inner join property_types as pt on pt.pt_id = prop.property_type
			   inner join property_lang_details as pld on pld.p_id = prop.p_id and pld.language = '$this->default_language'
			   and pld.title != '' 
			   where prop.p_id = '$kk'  and prop.status = 'publish' and prop.deleted = 'N' ";
        $property_result = $this->Common_model->commonQuery($sql);

        if ($property_result->num_rows() == 0) {
            unset($recentlyViewed[$kk]);
        }
    }
}

if (count($recentlyViewed) > 0) {

?>

    <style>
        .p_single_sidebar .widget ul.list_style {
            padding: 0px;
            margin: 0px;
        }

        .p_single_sidebar .widget ul.list_style li {
            list-style: none;
            border-bottom: 1px solid #f2f2f2;
            font-size: 12px;
            line-height: 20px;
            padding: 8px 2px 8px 12px;
            position: relative;
        }

        .p_single_sidebar .widget ul.list_style li:last-child {
            border-bottom: 0px none;
        }

        .p_single_sidebar .list_style li:before {
            font-family: FontAwesome;
            content: "\f105";
            font-size: 13px;
            position: absolute;
            left: 0;
            top: 8px;
        }

        .p_single_sidebar .widget ul.list_style li a {
            color: #777;
            font-size: 13px;
            line-height: 20px;
        }

        .p_single_sidebar .widget ul.list_style li a:hover {
            color: #364e68;
        }
    </style>
    <div class="bg-white widget border rounded text-left" id="recent_viewed">

        <h3 class="h4 text-black widget-title mb-3"><?php echo mlx_get_lang('Recent Viewed Properties'); ?></h3>

        <ul class="arrows_list list_style">
            <?php
            foreach ($recentlyViewed as $viewed_prop) {
            ?>
                <li><a href="<?php echo $viewed_prop['url']; ?>"><?php echo ucfirst($viewed_prop['title']); ?></a></li>
            <?php
            }
            ?>
        </ul>
    </div>

<?php } ?>