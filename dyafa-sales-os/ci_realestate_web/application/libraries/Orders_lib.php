<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class orders_lib
{

    public function get_order_details_callback()
    {
        extract($_POST);
        $CI = &get_instance();
        $CI->load->library('global_lib');
        $CI->load->model('Common_model');
        $theme = $CI->config->item('theme');
        $decId = $CI->global_lib->DecryptClientId($order_id);
        $result = $CI->Common_model->commonQuery("select * from orders where order_id = '$decId'");

        $item_row = $result->row();
        ob_start();
?>

        <div class="row">
            <div class="col-md-12">
                <h6 class="text-body text-sm  mb-3 text-center"><?php echo mlx_get_lang('Order Details'); ?></h6>
                <ul>
                    <li><?php echo mlx_get_lang('Order #'); ?><?php echo $order_id; ?></li>
                    <li><?php echo mlx_get_lang('Order Placed On'); ?><?php echo $CI->global_lib->relativeTime($item_row->order_created_on, true); ?></li>
                    <li><?php echo mlx_get_lang('Order Status'); ?><?php echo ucfirst($item_row->order_status); ?></li>
                </ul>

            </div>
        </div>
<?php
        $modal_body = ob_get_contents();
        ob_end_clean();

        header('Content-type: application/json');
        echo json_encode(array('modal_body' => $modal_body));
    }
}
?>