<?php
/** Trang "Cài đặt Magway EV": mọi thông tin liên hệ, slider, các hàng sản phẩm trang chủ sửa ở đây, không cần sửa code. */
if (!defined('ABSPATH')) exit;

function cbv_defaults() {
    return [
        'hotline'   => '0967 436 388',
        'phone'     => '',
        'zalo'      => '0967436388',
        'email'     => 'xemoitruong688@gmail.com',
        'brand_line'=> 'CÔNG TY TNHH XE ĐIỆN MÔI TRƯỜNG VIỆT NAM (Magway EV) - nhập khẩu, phân phối xe điện môi trường, xe quét rác hút bụi, máy vệ sinh công nghiệp, xe điện chở hàng, xe điện du lịch và sân golf',
        'addr_hn'   => 'Số 58 Đường Kênh Giữa, Xã Thiên Lộc, Hà Nội',
        'factory'   => '',
        'addr_hcm'  => '551/212 Lê Văn Khương, P. Tân Thới Hiệp, TP. Hồ Chí Minh',
        'facebook'  => 'https://www.facebook.com/MagwayElectricVehicles',
        'youtube'   => 'https://www.youtube.com/@MagwayEV',
        'map'       => '',
        'slides'    => "/uploads/images/a162d104-8afb-4928-9a63-bd0474dcfd39.jpg | /san-pham.html | Các dòng xe điện và máy vệ sinh Magway EV
/uploads/images/banner-xe-magway.jpg | /xe-dien-magway.html | Xe điện Magway
/uploads/images/slide/banner-chancee-2.jpg | /xe-quet-bui-nha-xuong.html | Xe quét bụi nhà xưởng
/uploads/images/slide/banner-1.jpg | /xe-dien-4-banh-cho-khach.html | Xe điện du lịch, sân golf",
        'home_rows' => "xe-quet-bui-nha-xuong | 8
xe-quet-rac-hut-bui-duong-do-thi | 8
xe-cho-rac-chay-dien | 8
xe-dien-cho-hang-trong-nha-may | 8
may-ve-sinh-lam-sach-cong-nghiep | 8
xe-dien-4-banh-cho-khach | 8",
        'commit'    => "Nhập khẩu, phân phối chính hãng
Tư vấn chọn xe theo mặt bằng thực tế
Bảo hành, bảo dưỡng tận nơi
Cho thuê máy chà sàn, xe quét rác",
        'sidebar_ad'=> '',
    ];
}
function cbv_opt($k) {
    $o = wp_parse_args(get_option('cbv_opts', []), cbv_defaults());
    return $o[$k] ?? '';
}
function cbv_lines($k) {
    $out = [];
    foreach (preg_split('/\r?\n/', (string) cbv_opt($k)) as $l) if (trim($l) !== '') $out[] = array_map('trim', explode('|', $l));
    return $out;
}
function cbv_tel($n) { return 'tel:' . preg_replace('/[^0-9+]/', '', $n); }

add_action('admin_menu', function () {
    add_menu_page('Cài đặt Magway EV', 'Cài đặt Magway EV', 'manage_options', 'cbv-settings', 'cbv_settings_page', 'dashicons-admin-generic', 3);
});
add_action('admin_init', function () {
    register_setting('cbv', 'cbv_opts', ['sanitize_callback' => function ($in) {
        $out = [];
        foreach (cbv_defaults() as $k => $v) $out[$k] = isset($in[$k]) ? (in_array($k, ['map'], true) ? esc_url_raw($in[$k]) : sanitize_textarea_field($in[$k])) : $v;
        return $out;
    }]);
});
function cbv_settings_page() {
    $f = [
        'Liên hệ' => ['hotline' => 'Hotline (tổng đài)', 'phone' => 'Số điện thoại thứ 2 (để trống nếu không có)', 'zalo' => 'Số Zalo (viết liền)', 'email' => 'Email nhận báo giá', 'brand_line' => 'Dòng giới thiệu ở chân trang',
                      'addr_hn' => 'Trụ sở Hà Nội', 'factory' => 'Xưởng / kho (để trống nếu không có)', 'addr_hcm' => 'Chi nhánh Hồ Chí Minh', 'facebook' => 'Link Facebook', 'youtube' => 'Link YouTube', 'map' => 'Link nhúng Google Maps (trang Liên hệ)'],
        'Trang chủ' => ['slides' => 'Ảnh slider: mỗi dòng "đường dẫn ảnh | link | mô tả ảnh"', 'home_rows' => 'Các hàng sản phẩm: mỗi dòng "đường dẫn danh mục | số sản phẩm"',
                        'commit' => 'Dải cam kết dưới slider: mỗi dòng 1 ý', 'sidebar_ad' => 'Banner cột trái ở trang tin tức, giới thiệu (để trống nếu không dùng; trang sản phẩm dùng banner riêng của từng nhóm, sửa trong Sản phẩm > Danh mục): "đường dẫn ảnh | link"'],
    ];
    $o = wp_parse_args(get_option('cbv_opts', []), cbv_defaults());
    echo '<div class="wrap"><h1>Cài đặt Magway EV</h1><p>Sửa thông tin hiển thị trên toàn website. Bấm "Lưu thay đổi" ở cuối trang.</p><form method="post" action="options.php">';
    settings_fields('cbv');
    foreach ($f as $group => $fields) {
        echo '<h2>' . esc_html($group) . '</h2><table class="form-table">';
        foreach ($fields as $k => $label) {
            $multi = in_array($k, ['slides', 'home_rows', 'commit'], true);
            echo '<tr><th><label for="cbv_' . $k . '">' . esc_html($label) . '</label></th><td>';
            echo $multi ? '<textarea class="large-text" rows="5" id="cbv_' . $k . '" name="cbv_opts[' . $k . ']">' . esc_textarea($o[$k]) . '</textarea>'
                        : '<input class="large-text" id="cbv_' . $k . '" name="cbv_opts[' . $k . ']" value="' . esc_attr($o[$k]) . '">';
            echo '</td></tr>';
        }
        echo '</table>';
    }
    submit_button('Lưu thay đổi');
    echo '</form></div>';
}
