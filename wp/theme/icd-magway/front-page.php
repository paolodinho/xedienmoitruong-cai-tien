<?php get_header(); $slides = cbv_lines('slides'); ?>
<h1 class="sr"><?= esc_html(get_bloginfo('name')) ?> - Xe điện môi trường, xe quét rác hút bụi, máy vệ sinh công nghiệp, xe điện chở hàng và xe điện du lịch</h1>
<?php if ($slides): ?>
<section class="slider" aria-label="Sản phẩm nổi bật" data-slider>
  <div class="slider__track">
    <?php foreach ($slides as $i => $s): ?>
    <a class="slider__item" href="<?= esc_url(home_url($s[1] ?? '/')) ?>">
      <img src="<?= esc_url(home_url($s[0])) ?>" alt="<?= esc_attr($s[2] ?? get_bloginfo('name')) ?>" width="1920" height="600" <?= $i ? 'loading="lazy"' : 'fetchpriority="high"' ?>>
    </a>
    <?php endforeach; ?>
  </div>
  <?php if (count($slides) > 1): ?>
  <button class="slider__nav slider__nav--prev" aria-label="Ảnh trước"></button>
  <button class="slider__nav slider__nav--next" aria-label="Ảnh sau"></button>
  <div class="slider__dots"><?php foreach ($slides as $i => $s) echo '<button aria-label="Ảnh ' . ($i + 1) . '"' . ($i ? '' : ' class="is-on"') . '></button>'; ?></div>
  <?php endif; ?>
</section>
<?php endif; ?>
<section class="commit">
  <ul class="wrap commit__list">
    <?php foreach (cbv_lines('commit') as $l) echo '<li>' . esc_html($l[0]) . '</li>'; ?>
  </ul>
</section>
<div class="wrap home">
  <section class="prow">
    <header class="sec-head"><h2 class="sec-head__h"><a href="<?= esc_url(get_permalink(wc_get_page_id('shop'))) ?>">Danh mục sản phẩm</a></h2></header>
    <div class="catnum">
      <?php foreach (cbv_product_cats() as $t) {
          if ($t->parent || $t->slug === 'uncategorized') continue;
          $p = get_posts(['post_type' => 'product', 'numberposts' => 1, 'meta_key' => '_thumbnail_id', 'tax_query' => [['taxonomy' => 'product_cat', 'terms' => $t->term_id]], 'orderby' => 'menu_order', 'order' => 'ASC']);
          echo '<a href="' . esc_url(get_term_link($t)) . '">' . ($p ? get_the_post_thumbnail($p[0], 'cbv-card', ['loading' => 'lazy', 'alt' => esc_attr($t->name)]) : '<span class="noimg">Ảnh đang cập nhật</span>') . '<span class="catnum__t"><span>' . esc_html($t->name) . '<small>' . (int) $t->count . ' sản phẩm</small></span></span></a>';
      } ?>
    </div>
  </section>

  <?php foreach (cbv_lines('home_rows') as $row):
      $t = get_term_by('slug', $row[0], 'product_cat'); if (!$t) continue;
      $q = new WP_Query(['post_type' => 'product', 'posts_per_page' => (int) ($row[1] ?? 8) ?: 8, 'no_found_rows' => true, 'orderby' => 'menu_order', 'order' => 'ASC',
          'tax_query' => [['taxonomy' => 'product_cat', 'terms' => $t->term_id]]]);
      if (!$q->have_posts()) continue; ?>
  <section class="prow">
    <header class="sec-head">
      <h2 class="sec-head__h"><a href="<?= esc_url(get_term_link($t)) ?>"><?= esc_html($t->name) ?></a></h2>
      <a class="sec-head__more" href="<?= esc_url(get_term_link($t)) ?>">Xem tất cả <?= (int) $t->count ?> mẫu</a>
    </header>
    <div class="grid grid--4">
      <?php while ($q->have_posts()) { $q->the_post(); get_template_part('template-parts/card-product'); } wp_reset_postdata(); ?>
    </div>
  </section>
  <?php endforeach; ?>

  <?php $news = new WP_Query(['post_type' => 'post', 'posts_per_page' => 4, 'no_found_rows' => true, 'category_name' => 'tin-tuc,tin-tuc-moi-truong,tin-noi-bo-tin-du-an,huong-dan']);
  if ($news->have_posts()): ?>
  <section class="prow">
    <header class="sec-head"><h2 class="sec-head__h"><a href="<?= esc_url(home_url('/tin-tuc.html')) ?>">Tin tức nổi bật</a></h2><a class="sec-head__more" href="<?= esc_url(home_url('/tin-tuc.html')) ?>">Xem tất cả</a></header>
    <div class="grid grid--4">
      <?php while ($news->have_posts()) { $news->the_post(); get_template_part('template-parts/card-post'); } wp_reset_postdata(); ?>
    </div>
  </section>
  <?php endif; ?>
</div>
<?php get_footer();
