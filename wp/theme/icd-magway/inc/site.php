<?php
/** Hàm riêng của site: giá hiển thị, banner nhóm tự lấy ảnh SP đầu nhóm. */
if (!defined('ABSPATH')) exit;
function cbv_price_html($id) {
    $p = get_post_meta($id, '_price', true);
    return ($p !== '' && (int) $p > 0) ? number_format((int) $p, 0, ',', '.') . 'đ' : 'Liên hệ';
}
function cbv_group_photo($term) {
    $p = get_posts(['post_type' => 'product', 'numberposts' => 1, 'orderby' => 'menu_order', 'order' => 'ASC', 'meta_key' => '_thumbnail_id',
        'tax_query' => [['taxonomy' => 'product_cat', 'terms' => $term->term_id, 'include_children' => true]]]);
    return $p ? get_the_post_thumbnail_url($p[0], 'medium_large') : '';
}
