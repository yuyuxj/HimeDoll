<?php
defined('ABSPATH') || exit;
get_header();
?>
<main id="main-content" class="section">
    <div class="container content-width">
        <p class="eyebrow">404</p>
        <h1>ページが見つかりませんでした</h1>
        <p>URLをご確認いただくか、トップページからお探しください。</p>
        <a class="button button--primary" href="<?php echo esc_url(home_url('/')); ?>">トップページへ</a>
    </div>
</main>
<?php get_footer(); ?>
