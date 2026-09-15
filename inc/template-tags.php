<?php
/**
 * Reusable template tags.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wdod_posted_on' ) ) :
	/**
	 * Print the post date and author meta.
	 *
	 * @return void
	 */
	function wdod_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		printf(
			'<span class="wdod-meta__date posted-on"><a href="%1$s" rel="bookmark">%2$s</a></span> <span class="wdod-meta__author byline">%3$s <span class="author vcard"><a class="url fn n" href="%4$s">%5$s</a></span></span>',
			esc_url( get_permalink() ),
			$time_string, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
			esc_html_x( 'by', 'post author', 'wdod-starter' ),
			esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}
endif;

if ( ! function_exists( 'wdod_post_thumbnail' ) ) :
	/**
	 * Print the post thumbnail, linked on archives.
	 *
	 * @param string $size Registered image size.
	 * @return void
	 */
	function wdod_post_thumbnail( $size = 'wdod-card' ) {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) {
			echo '<div class="wdod-entry__thumbnail post-thumbnail">';
			the_post_thumbnail( $size, array( 'loading' => 'eager' ) );
			echo '</div>';
			return;
		}

		printf(
			'<a class="wdod-entry__thumbnail post-thumbnail" href="%s" aria-hidden="true" tabindex="-1">',
			esc_url( get_permalink() )
		);
		the_post_thumbnail(
			$size,
			array(
				'alt' => the_title_attribute( array( 'echo' => false ) ),
			)
		);
		echo '</a>';
	}
endif;

if ( ! function_exists( 'wdod_breadcrumbs' ) ) :
	/**
	 * Print breadcrumbs.
	 *
	 * Delegates to WooCommerce or Yoast SEO when present, otherwise renders
	 * a hierarchy-aware trail of its own.
	 *
	 * @return void
	 */
	function wdod_breadcrumbs() {
		if ( is_front_page() ) {
			return;
		}

		if ( function_exists( 'is_woocommerce' ) && function_exists( 'woocommerce_breadcrumb' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
			woocommerce_breadcrumb(
				array(
					'delimiter'   => '',
					'wrap_before' => '<nav class="wdod-breadcrumbs woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'wdod-starter' ) . '">',
					'wrap_after'  => '</nav>',
					'before'      => '<span class="wdod-breadcrumbs__item">',
					'after'       => '</span>',
				)
			);
			return;
		}

		if ( function_exists( 'yoast_breadcrumb' ) ) {
			yoast_breadcrumb( '<nav class="wdod-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'wdod-starter' ) . '">', '</nav>' );
			return;
		}

		$items   = array();
		$items[] = array(
			'label' => esc_html__( 'Home', 'wdod-starter' ),
			'url'   => home_url( '/' ),
		);

		$blog_page = (int) get_option( 'page_for_posts' );

		if ( is_home() && $blog_page ) {
			$items[] = array( 'label' => get_the_title( $blog_page ) );
		} elseif ( is_singular( 'post' ) ) {
			if ( $blog_page ) {
				$items[] = array(
					'label' => get_the_title( $blog_page ),
					'url'   => get_permalink( $blog_page ),
				);
			}

			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				$category  = $categories[0];
				$ancestors = array_reverse( get_ancestors( $category->term_id, 'category' ) );

				foreach ( $ancestors as $ancestor_id ) {
					$ancestor = get_term( $ancestor_id, 'category' );
					if ( $ancestor && ! is_wp_error( $ancestor ) ) {
						$items[] = array(
							'label' => $ancestor->name,
							'url'   => get_term_link( $ancestor ),
						);
					}
				}

				$items[] = array(
					'label' => $category->name,
					'url'   => get_term_link( $category ),
				);
			}

			$items[] = array( 'label' => get_the_title() );
		} elseif ( is_singular() ) {
			$post_type = get_post_type_object( get_post_type() );

			if ( $post_type && $post_type->has_archive && 'page' !== $post_type->name ) {
				$items[] = array(
					'label' => $post_type->labels->name,
					'url'   => get_post_type_archive_link( $post_type->name ),
				);
			}

			$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
			foreach ( $ancestors as $ancestor_id ) {
				$items[] = array(
					'label' => get_the_title( $ancestor_id ),
					'url'   => get_permalink( $ancestor_id ),
				);
			}

			$items[] = array( 'label' => get_the_title() );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();

			if ( $term instanceof WP_Term ) {
				$ancestors = array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) );
				foreach ( $ancestors as $ancestor_id ) {
					$ancestor = get_term( $ancestor_id, $term->taxonomy );
					if ( $ancestor && ! is_wp_error( $ancestor ) ) {
						$items[] = array(
							'label' => $ancestor->name,
							'url'   => get_term_link( $ancestor ),
						);
					}
				}

				$items[] = array( 'label' => $term->name );
			}
		} elseif ( is_post_type_archive() ) {
			$items[] = array( 'label' => post_type_archive_title( '', false ) );
		} elseif ( is_author() ) {
			$items[] = array( 'label' => get_the_author() );
		} elseif ( is_year() ) {
			$items[] = array( 'label' => get_the_date( 'Y' ) );
		} elseif ( is_month() ) {
			$items[] = array( 'label' => get_the_date( 'F Y' ) );
		} elseif ( is_day() ) {
			$items[] = array( 'label' => get_the_date() );
		} elseif ( is_search() ) {
			$items[] = array(
				/* translators: %s: search query. */
				'label' => sprintf( esc_html__( 'Search results for "%s"', 'wdod-starter' ), get_search_query() ),
			);
		} elseif ( is_404() ) {
			$items[] = array( 'label' => esc_html__( 'Page not found', 'wdod-starter' ) );
		}

		/**
		 * Filter the breadcrumb items before rendering.
		 *
		 * @param array $items Each item has a "label" and optional "url".
		 */
		$items = apply_filters( 'wdod_breadcrumb_items', $items );

		if ( count( $items ) < 2 ) {
			return;
		}

		$last = count( $items ) - 1;

		echo '<nav class="wdod-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'wdod-starter' ) . '"><ol class="wdod-breadcrumbs__list">';

		foreach ( $items as $index => $item ) {
			$label = isset( $item['label'] ) ? $item['label'] : '';

			if ( $index === $last || empty( $item['url'] ) ) {
				printf( '<li class="wdod-breadcrumbs__item" aria-current="page">%s</li>', esc_html( $label ) );
			} else {
				printf( '<li class="wdod-breadcrumbs__item"><a href="%1$s">%2$s</a></li>', esc_url( $item['url'] ), esc_html( $label ) );
			}
		}

		echo '</ol></nav>';
	}
endif;

if ( ! function_exists( 'wdod_pagination' ) ) :
	/**
	 * Print numeric pagination for archive loops.
	 *
	 * @param array $args Optional overrides for the_posts_pagination().
	 * @return void
	 */
	function wdod_pagination( $args = array() ) {
		$defaults = array(
			'mid_size'           => 2,
			'prev_text'          => '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'wdod-starter' ) . '</span><span aria-hidden="true">&larr;</span>',
			'next_text'          => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'wdod-starter' ) . '</span><span aria-hidden="true">&rarr;</span>',
			'screen_reader_text' => esc_html__( 'Posts navigation', 'wdod-starter' ),
			'class'              => 'wdod-pagination',
		);

		the_posts_pagination( wp_parse_args( $args, $defaults ) );
	}
endif;

if ( ! function_exists( 'wdod_entry_footer' ) ) :
	/**
	 * Print categories, tags and edit link for a post.
	 *
	 * @return void
	 */
	function wdod_entry_footer() {
		if ( 'post' === get_post_type() ) {
			$categories_list = get_the_category_list( ', ' );
			if ( $categories_list ) {
				/* translators: %s: list of categories. */
				printf( '<span class="wdod-meta__cats cat-links">' . esc_html__( 'Posted in %s', 'wdod-starter' ) . '</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-escaped.
			}

			$tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ) {
				/* translators: %s: list of tags. */
				printf( ' <span class="wdod-meta__tags tags-links">' . esc_html__( 'Tagged %s', 'wdod-starter' ) . '</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-escaped.
			}
		}

		edit_post_link(
			sprintf(
				/* translators: %s: post title. */
				esc_html__( 'Edit %s', 'wdod-starter' ),
				'<span class="screen-reader-text">' . get_the_title() . '</span>'
			),
			' <span class="wdod-meta__edit edit-link">',
			'</span>'
		);
	}
endif;
