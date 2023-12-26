<?php
/**
 * Storipress
 *
 * @package Storipress
 */

declare(strict_types=1);

namespace Storipress\Storipress\Triggers;

use Storipress;

/**
 * The acf data trigger.
 *
 * @since 0.0.14
 */
final class ACF_Data extends Trigger {
	/**
	 * {@inheritDoc}
	 */
	public function validate(): bool {
		return Storipress::instance()->core->is_connected();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array{}
	 */
	public function run(): array {
		$posts = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => array( 'acf-field-group', 'acf-field' ),
			)
		);

		return array_map(
			function ( $post ) {
				return array(
					'id'         => (string) $post->ID,
					// Acf field group ID, which will be null if it's a field group.
					'post_id'    => empty( $post->post_parent ) ? null : (string) $post->post_parent,
					// This is an ACF type, which will be either 'acf-field' or 'acf-field-group'.
					'type'       => $post->post_type,
					// Field label.
					'title'      => $post->post_title,
					// The unique name that is automatically generated by ACF.
					'slug'       => $post->post_name,
					// Field name.
					'excerpt'    => empty( $post->post_excerpt ) ? null : $post->post_excerpt,
					// The detailed settings of a custom field, including types, validation, etc.
					'content'    => $post->post_content,
					'created_at' => get_the_date( 'U', $post ),
					'updated_at' => get_the_modified_date( 'U', $post ),
				);
			},
			$posts
		);
	}
}
