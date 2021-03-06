<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package AMP_Compatible_Theme
 */

if ( ! function_exists( 'amp_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function amp_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$byline = sprintf(
		esc_html_x( 'Written by %s', 'post author', 'amp' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	$posted_on = sprintf(
		esc_html_x( 'Published on %s', 'post date', 'amp' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	echo '<span class="byline">' . $byline . '</span> <span class="posted-on"> ' . $posted_on . '</span>'; // WPCS: XSS OK.

	// Comments info and link
	amp_num_comments();
	// Edit post link
	amp_get_edit_post_link();
}
endif;

if ( ! function_exists( 'amp_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function amp_entry_footer() {
	// Hide tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'amp' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'amp' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

}
endif;

function amp_the_category_list() {
	/* translators: used between list items, there is a space after the comma */
	$categories_list = get_the_category_list( esc_html__( ', ', 'amp' ) );
	if ( $categories_list && amp_categorized_blog() ) {
		printf( '<span class="cat-links">' . esc_html__( '%1$s', 'amp' ) . '</span>', $categories_list ); // WPCS: XSS OK.
	}
}

function amp_num_comments() {
	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo ' <span class="comments-link"><span class="extra">Discussion</span>';
		/* translators: %s: post title */
		comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'amp' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
		echo '</span>';
	}
}

function amp_get_edit_post_link() {
	edit_post_link(
		sprintf(
		/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'amp' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		' <span class="edit-link"><span class="extra">Admin</span>',
		'</span>'
	);
}
/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function amp_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'amp_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'amp_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so amp_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so amp_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in amp_categorized_blog.
 */
function amp_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'amp_categories' );
}
add_action( 'edit_category', 'amp_category_transient_flusher' );
add_action( 'save_post',     'amp_category_transient_flusher' );

/**
 * Post navigation (previous / next post) for single posts.
 * Accounting for accessibility.
 */
function amp_post_navigation() {
	the_post_navigation( array(
		'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next', 'amp' ) . '</span> ' .
		               '<span class="screen-reader-text">' . __( 'Next post:', 'amp' ) . '</span> ' .
		               '<span class="post-title">%title</span>',
		'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous', 'amp' ) . '</span> ' .
		               '<span class="screen-reader-text">' . __( 'Previous post:', 'amp' ) . '</span> ' .
		               '<span class="post-title">%title</span>',
	) );
}

/**
 * Customize symbol at the end of an excerpt
 */
function amp_excerpt_more() {
	return "...";
}
add_filter("excerpt_more", "amp_excerpt_more");

/**
 * Customize length of an excerpt
 */
function amp_excerpt_length( $length ) {
	return 100;
}
add_filter("excerpt_length", "amp_excerpt_length");