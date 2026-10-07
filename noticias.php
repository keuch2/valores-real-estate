<?php
require __DIR__ . '/inc/layout.php';
page_header('noticias', 'Noticias', 'Valores Real Estate en los medios.');
?>
<section class="wrap page-intro">
  <div class="eyebrow">Noticias</div>
  <div class="intro-row">
    <h1 class="display-1">En los medios</h1>
    <div class="pill-row" data-news-filters>
      <?php foreach (NEWS_TAGS as $i => $t): ?>
        <button type="button" class="pill<?= $i === 0 ? ' is-on' : '' ?>" data-filter="<?= e($t) ?>"><?= e($t) ?></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="wrap section-last">
  <div class="grid-news" data-news-grid>
    <?php foreach (NEWS as $a) news_card($a, true); ?>
  </div>
</section>
<?php page_footer(); ?>
