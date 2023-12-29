<?php
/**
 * Storipress
 *
 * @package Storipress
 */

declare(strict_types=1);

namespace Storipress\Storipress\Triggers;

use Storipress;
use WP_Post;

/**
 * The acf data trigger.
 *
 * @since 0.0.14
 */
final class Update_Yoast_Seo_Metadata extends Trigger {

	/**
	 * Plugin file.
	 *
	 * @var string
	 */
	public $file = 'wordpress-seo/wp-seo.php';

	/**
	 * The post id.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * The seo title.
	 *
	 * @var array{
	 *    seo_title?: string,
	 *    seo_description?: string,
	 *    og_title?: string,
	 *    og_description?: string,
	 *    og_image_id?: int
	 * }
	 */
	public $options;

	/**
	 * The seo description.
	 *
	 * @var string|null
	 */
	public $description;

	/**
	 * Constructor.
	 *
	 * @param int                                                                                                                 $post_id The post id.
	 * @param array{seo_title?: string, seo_description?: string, og_title?: string, og_description?: string, og_image_id?: int } $options The seo options.
	 */
	public function __construct( int $post_id, array $options ) {
		$this->post_id = $post_id;

		$this->options = $options;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate(): bool {
		if ( ! Storipress::instance()->core->is_connected() ) {
			return false;
		}

		// Needs to include the plugin function on a non-admin page.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Ensure yoast seo is installed.
		if ( ! in_array( $this->file, array_keys( get_plugins() ), true ) ) {
			return false;
		}

		// Ensure yoast seo is active.
		if ( ! is_plugin_active( $this->file ) ) {
			return false;
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array{}
	 */
	public function run(): array {
		if ( isset( $this->options['seo_title'] ) ) {
			update_metadata( 'post', $this->post_id, '_yoast_wpseo_title', $this->options['seo_title'] );
		}

		if ( isset( $this->options['seo_description'] ) ) {
			update_metadata( 'post', $this->post_id, '_yoast_wpseo_metadesc', $this->options['seo_description'] );
		}

		if ( isset( $this->options['og_title'] ) ) {
			update_metadata( 'post', $this->post_id, '_yoast_wpseo_opengraph-title', $this->options['og_title'] );
		}

		if ( isset( $this->options['og_description'] ) ) {
			update_metadata( 'post', $this->post_id, '_yoast_wpseo_opengraph-description', $this->options['og_description'] );
		}

		if ( isset( $this->options['og_image_id'] ) ) {
			if ( -1 === $this->options['og_image_id'] ) {
				delete_metadata( 'post', $this->post_id, '_yoast_wpseo_opengraph-image-id' );

				delete_metadata( 'post', $this->post_id, '_yoast_wpseo_opengraph-image' );
			} else {
				$post = get_post( $this->options['og_image_id'] );

				if ( $post instanceof WP_Post && 'attachment' === $post->post_type ) {
					update_metadata( 'post', $this->post_id, '_yoast_wpseo_opengraph-image-id', $this->options['og_image_id'] );

					update_metadata( 'post', $this->post_id, '_yoast_wpseo_opengraph-image', $post->guid );
				}
			}
		}

		return array();
	}
}
