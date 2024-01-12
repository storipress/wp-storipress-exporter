<?php
/**
 * Storipress
 *
 * @package Storipress
 */

namespace Storipress\Storipress\Actions;

/**
 * Tag deleted hook action.
 *
 * @since 0.0.14
 */
class Tag_Deleted extends Action {

	/**
	 * Webhook topic.
	 *
	 * @var string
	 */
	public $topic = 'tag_deleted';

	/**
	 * The official document link.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/delete_taxonomy/
	 */
	public function register(): void {
		add_action( 'delete_post_tag', array( &$this, 'handle' ), 10, 2 );
	}

	/**
	 * Hook action.
	 *
	 * @param int $term_id Term ID.
	 * @param int $taxonomy_id Term taxonomy ID.
	 * @return void
	 */
	public function handle( $term_id, $taxonomy_id ): void {
		$this->send(
			array(
				'term_id'     => $term_id,
				'taxonomy_id' => $taxonomy_id,
			)
		);
	}
}
