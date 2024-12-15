function popular_tags_accordion_shortcode($atts) {

    global $wpdb;
    ob_start();

    // Get most selling product IDs
    $best_selling_products = wc_get_products(array(
        'status'   => 'publish',
        'limit'    => -1, // Fetch all products
        'orderby'  => 'total_sales',
        'order'    => 'DESC',
        'return'   => 'ids', // Only IDs
    ));

    // Fetch tags of best-selling products
    $tags_popular = wp_get_object_terms($best_selling_products, 'product_tag', array(
        'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => true,
    ));

    // Limit to 30 tags for the "Best Selling" section
    $limited_tags = array_slice($tags_popular, 0, 30);

    // Fetch most popular product tags
    $tags = get_terms(array(
        'taxonomy'   => 'product_tag',
		'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => true,
    ));

    if (empty($tags) || is_wp_error($tags)) {
        return '<p>No popular tags found.</p>';
    }

    // Group tags into custom alphabetical ranges
    $ranges = array(
        'A-B' => range('A', 'B'),
        'C-G' => range('C', 'G'),
        'H-L' => range('H', 'L'),
        'M-P' => range('M', 'P'),
        'Q-S' => range('Q', 'S'),
        'T-V' => range('T', 'V'),
        'W-Z' => range('W', 'Z'),
    );

    $grouped_tags = array();

    // Group tags based on their first letter and ranges
    foreach ($tags as $tag) {
        $first_letter = strtoupper(substr($tag->name, 0, 1));

        foreach ($ranges as $range_label => $letters) {
            if (in_array($first_letter, $letters)) {
                $grouped_tags[$range_label][] = $tag;
                break;
            }
        }
    }

    // Accordion Output
    ?>
    <div class="popular-tags-accordion">
        <!-- Best Selling Section -->
        <div class="accordion-item">
            <div class="accordion-title" onclick="toggleAccordion(this)">
                <span>Best Selling</span>
                <span class="accordion-icon">+</span>
            </div>
            <div class="accordion-content" style="display: none;">
                <?php if (empty($limited_tags)) { ?>
                    <p>No best-selling product tags found.</p>
                <?php } else { ?>
                    <ul>
                        <?php foreach ($limited_tags as $tag): ?>
                            <li>
                                <a href="<?php echo esc_url(get_term_link($tag)); ?>">
                                    <?php echo esc_html($tag->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php } ?>
            </div>
        </div>

        <!-- Alphabetical Ranges -->
        <?php foreach ($grouped_tags as $range => $tags_group): ?>
            <div class="accordion-item">
                <div class="accordion-title" onclick="toggleAccordion(this)">
                    <span><?php echo esc_html("TAGS: $range"); ?></span>
                    <span class="accordion-icon">+</span>
                </div>
                <div class="accordion-content" style="display: none;">
                    <ul>
                        <?php foreach ($tags_group as $tag): ?>
                            <li>
                                <a href="<?php echo esc_url(get_term_link($tag)); ?>">
                                    <?php echo esc_html($tag->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Inline CSS -->
    <style>
        .popular-tags-accordion .accordion-item {
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .accordion-title {
            background-color: #ffc107;
            padding: 10px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            color: #333;
        }

        .accordion-content {
            padding: 10px;
            display: none;
            background-color: #fff;
        }

        .accordion-content ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .accordion-content li {
            display: inline-block;
            padding-right: 8px;
            line-height: 1.5;
            border-right: 1px solid #ccc;
            margin-right: 8px;
        }

        .accordion-content li:last-child {
            border-right: none;
            margin-right: 0;
        }

        .accordion-content a {
            text-decoration: underline;
            color: #333;
        }

        .accordion-content a:hover {
            text-decoration: underline;
            color: #000;
        }
    </style>

    <!-- Inline JS -->
    <script>
        function toggleAccordion(element) {
            const content = element.nextElementSibling;
            const icon = element.querySelector('.accordion-icon');

            if (content.style.display === 'none' || content.style.display === '') {
                content.style.display = 'block';
                icon.innerHTML = '-';
            } else {
                content.style.display = 'none';
                icon.innerHTML = '+';
            }
        }
    </script>
    <?php

    return ob_get_clean();
}

add_shortcode('popular_tags_accordion', 'popular_tags_accordion_shortcode');
