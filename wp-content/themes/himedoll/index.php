<?php
defined('ABSPATH') || exit;
get_header();
?>
<main id="main-content" class="section">
    <div class="container content-width">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('content-card'); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e('コンテンツが見つかりませんでした。', 'himedoll'); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
