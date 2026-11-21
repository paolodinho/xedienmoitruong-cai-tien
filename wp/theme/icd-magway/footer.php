</main>
<?php if (!is_page('lien-he')) get_template_part('template-parts/quote-form'); ?>
<footer class="footer">
  <div class="wrap footer__grid">
    <div class="footer__col footer__col--info">
      <h2 class="footer__h">Thông tin liên hệ</h2>
      <?php $bl = cbv_opt('brand_line'); $bp = preg_split('/\s+-\s+/u', $bl, 2); ?>
      <p class="footer__brand"><strong><?= esc_html($bp[0]) ?></strong></p>
      <?php if (!empty($bp[1])): ?><p class="footer__desc"><?= esc_html(mb_strtoupper(mb_substr($bp[1],0,1)).mb_substr($bp[1],1)) ?></p><?php endif; ?>
      <ul class="footer__list">
        <li><b>Trụ sở Hà Nội:</b> <?= esc_html(cbv_opt('addr_hn')) ?></li>
        <?php if (cbv_opt('factory')): ?><li><b>Nhà máy sản xuất:</b> <?= esc_html(cbv_opt('factory')) ?></li><?php endif; ?>
        <li><b>Chi nhánh Hồ Chí Minh:</b> <?= esc_html(cbv_opt('addr_hcm')) ?></li>
        <li><b>Hotline:</b> <a href="<?= esc_attr(cbv_tel(cbv_opt('hotline'))) ?>"><?= esc_html(cbv_opt('hotline')) ?></a><?php if (cbv_opt('phone')): ?> - <a href="<?= esc_attr(cbv_tel(cbv_opt('phone'))) ?>"><?= esc_html(cbv_opt('phone')) ?></a><?php endif; ?></li>
        <li><b>Email:</b> <a href="mailto:<?= esc_attr(cbv_opt('email')) ?>"><?= esc_html(cbv_opt('email')) ?></a></li>
      </ul>
    </div>
    <div class="footer__col">
      <h2 class="footer__h"><a href="<?= esc_url(get_permalink(wc_get_page_id('shop'))) ?>">Danh mục sản phẩm</a></h2>
      <ul class="footer__links">
        <?php foreach (cbv_product_cats() as $t) if (!$t->parent) echo '<li><a href="' . esc_url(get_term_link($t)) . '">' . esc_html($t->name) . '</a></li>'; ?>
      </ul>
    </div>
    <div class="footer__col">
      <h2 class="footer__h">Về Magway EV</h2>
      <ul class="footer__links">
        <li><a href="<?= esc_url(home_url('/gioi-thieu.html')) ?>">Giới thiệu công ty</a></li>
        <li><a href="<?= esc_url(home_url('/dich-vu.html')) ?>">Dịch vụ cho thuê</a></li>
        <li><a href="<?= esc_url(home_url('/huong-dan.html')) ?>">Hướng dẫn</a></li>
        <li><a href="<?= esc_url(home_url('/chinh-sach.html')) ?>">Chính sách</a></li>
        <li><a href="<?= esc_url(home_url('/tin-tuc.html')) ?>">Tin tức</a></li>
        <li><a href="<?= esc_url(home_url('/tuyen-dung.html')) ?>">Tuyển dụng</a></li>
        <li><a href="<?= esc_url(home_url('/lien-he.html')) ?>">Liên hệ</a></li>
      </ul>
      <div class="footer__social">
        <?php if (cbv_opt('facebook')): ?><a href="<?= esc_url(cbv_opt('facebook')) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
        <?php if (cbv_opt('youtube')): ?><a href="<?= esc_url(cbv_opt('youtube')) ?>" target="_blank" rel="noopener">YouTube</a><?php endif; ?>
        <a href="https://zalo.me/<?= esc_attr(cbv_opt('zalo')) ?>" target="_blank" rel="noopener">Zalo</a>
      </div>
    </div>
  </div>
  <div class="footer__bottom"><div class="wrap">© <?= date('Y') ?> Công ty TNHH Xe điện Môi trường Việt Nam - Magway EV.</div></div>
</footer>
<div class="float" aria-label="Liên hệ nhanh">
  <a class="float__btn float__btn--call" href="<?= esc_attr(cbv_tel(cbv_opt('hotline'))) ?>" aria-label="Gọi hotline"><i><svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><path fill="currentColor" d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/></svg></i><span><?= esc_html(cbv_opt('hotline')) ?></span></a>
  <a class="float__btn float__btn--zalo" href="https://zalo.me/<?= esc_attr(cbv_opt('zalo')) ?>" target="_blank" rel="noopener" aria-label="Chat Zalo"><i><svg viewBox="0 0 28 28" width="30" height="30" aria-hidden="true"><path fill="#fff" d="M14 2.5C7.4 2.5 2.5 7 2.5 12.7c0 3.1 1.5 5.8 4 7.7L5.7 25l4.6-2.4c1.2.4 2.4.5 3.7.5 6.6 0 11.5-4.5 11.5-10.4S20.600 2.5 14 2.5z"/><path fill="#0068FF" d="M8.800 8.600h9.800v1.900l-6.400 7.100h6.500v1.900H8.600v-1.900L15 10.500H8.800z"/></svg></i><span><b class="lb">Zalo </b><?= esc_html(cbv_phone_fmt(cbv_opt('zalo'))) ?></span></a>
  <a class="float__btn float__btn--quote" href="#bao-gia" aria-label="Nhận báo giá"><i><svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path fill="currentColor" d="M6 2h8l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm7 1.5V8h4.5L13 3.5zM8 12v1.6h8V12H8zm0 3.6v1.6h8v-1.6H8z"/></svg></i><span>Báo giá</span></a>
  <button class="float__top" id="totop" aria-label="Lên đầu trang"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M12 7l7 7-1.4 1.4L12 9.8l-5.6 5.6L5 14z"/></svg></button>
</div>
<?php wp_footer(); ?>
</body>
</html>
