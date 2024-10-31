<?php
namespace qqworld\extension;
use qqworld\core\extension;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Æ·ÅÆÀà
class brand extends extension {
	var $free = 1;
	var $slug = 'brand-taxonomy';
	var $taxonomy_brand = 'product_brand';

	public function init() {
		add_filter( 'qqworld-woocommerce-assistant-extensions', array($this, 'register') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
		add_action( 'admin_init', array($this, 'register_settings') );

		if ($this->options->extension_brands_taxonomy_enabled != 1) return;

		add_action( 'init', array($this, 'register_taxonomy') );

		add_action( $this->taxonomy_brand . '_add_form_fields', array( $this, 'add_brand_taxonomy_fields' ), 15, 1 );
		add_action( $this->taxonomy_brand . '_edit_form_fields', array( $this, 'edit_brand_taxonomy_fields' ), 15, 1 );
		add_action( 'created_term', array( $this, 'save_brand_taxonomy_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_brand_taxonomy_fields' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		// add taxonomy columns
		add_filter( 'manage_edit-' . $this->taxonomy_brand . '_columns', array( $this, 'brand_taxonomy_columns' ), 15 );
		add_filter( 'manage_' . $this->taxonomy_brand . '_custom_column', array( $this, 'brand_taxonomy_column' ), 15, 3 );

		// Taxonomy page descriptions
		add_action( $this->taxonomy_brand . '_pre_add_form', array( $this, 'brand_taxonomy_description' ) );
	}

	public function register($extensions) {
		$this->label = _x('Brand Taxonomy', 'extension', $this->text_domain);
		$this->description = _x('The Brand Taxonomy of Products.', 'extension', $this->text_domain);
		$this->image = QAFW_URL . "images/extensions/{$this->slug}.png";
		$extensions[] = $this;
		return $extensions;
	}

	public function page() {
?>
<div class="wrap" id="qqworld-woocommerce-assistant-container">
	<h2><?php echo $this->label; ?></h2>
	<form action="options.php" method="post" id="update-form">
		<?php settings_fields($this->text_domain.'-extension-'.$this->slug); ?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="extension-brands-add-on"><?php _e('Brands', $this->text_domain); ?></label>
						<span class="woocommerce-help-tip" data-header="<?php _e('Brands', $this->text_domain); ?>" data-content="<?php _e('Brands Taxonomy of Product.', $this->text_domain); ?>"></span>
					</th>
					<td class="forminp">
						<label><input type="checkbox" id="extension-brands-add-on" name="QAWC_EXTENSION_<?php echo $this->slug; ?>[enabled]" value="1" <?php checked($this->options->extension_brands_taxonomy_enabled, 1);?> /> <?php _e('Enabled', $this->text_domain); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
<?php
	}

	public function brand_taxonomy_description() {
		echo wpautop( __( 'Product brands for your store can be managed here. To display more brands here, click on "screen options" link on top of the page.', $this->text_domain ) );
	}

	public function brand_taxonomy_columns( $columns ) {
		$new_columns          = array();
		$new_columns['thumb'] = __( 'Image', $this->text_domain );
		return array_merge( $new_columns, $columns );
	}

	public function brand_taxonomy_column( $columns, $column, $id ) {
		if ( 'thumb' == $column ) {
			$thumbnail_id = get_woocommerce_term_meta( $id, 'thumbnail_id', true );
			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = wc_placeholder_img_src();
			}
			$image = str_replace( ' ', '%20', $image );
			$columns = '<img src="' . esc_url( $image ) . '" alt="' . __( 'Thumbnail', $this->text_domain ) . '" class="wp-post-image" height="48" width="48" />';
		}
		return $columns;
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if( $screen->id == 'edit-' . $this->taxonomy_brand ){
			wp_enqueue_media();
			wp_enqueue_script( $this->taxonomy_brand, QAFW_URL . 'js/edit-taxonomy-brand.js', array( 'jquery' ), false, true );
			wp_localize_script( $this->taxonomy_brand, 'QAWC_EXTENSION', array(
				'labels' => array(
					'upload_file_frame_title' => __( 'Choose an image', $this->text_domain ),
					'upload_file_frame_button' => __( 'Use image', $this->text_domain )
				),
				'wc_placeholder_img_src' => wc_placeholder_img_src()
			) );
		}
	}

	public function add_brand_taxonomy_fields( $term ) {
?>
	<div class="form-field">
		<label><?php _e( 'Thumbnail', $this->text_domain ); ?></label>
		<div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo wc_placeholder_img_src(); ?>" width="60px" height="60px" /></div>
		<div style="line-height:60px;">
			<input type="hidden" id="product_brand_thumbnail_id" class="upload_image_id" name="product_brand_thumbnail_id" />
			<button id="product_brand_thumbnail_upload" type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', $this->text_domain ); ?></button>
			<button id="product_brand_thumbnail_remove" type="button" class="remove_image_button button"><?php _e( 'Remove image', $this->text_domain ); ?></button>
		</div>
		<div class="clear"></div>
	</div>
<?php
	}

	public function edit_brand_taxonomy_fields( $term ) {
		$thumbnail_id = absint( get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true ) );
		$image = $thumbnail_id ? wp_get_attachment_thumb_url( $thumbnail_id ) : wc_placeholder_img_src();

?>
	<tr class="form-field">
		<th scope="row" valign="top"><label><?php _e( 'Thumbnail', $this->text_domain ); ?></label></th>
		<td>
			<div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="product_brand_thumbnail_id" class="upload_image_id" name="product_brand_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
				<button id="product_brand_thumbnail_upload" type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', $this->text_domain ); ?></button>
				<button id="product_brand_thumbnail_remove" type="button" class="remove_image_button button"><?php _e( 'Remove image', $this->text_domain ); ?></button>
			</div>
			<div class="clear"></div>
		</td>
	</tr>
<?php
	}

	public function save_brand_taxonomy_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['product_brand_thumbnail_id'] ) && $this->taxonomy_brand === $taxonomy ) {
			update_woocommerce_term_meta( $term_id, 'thumbnail_id', absint( $_POST['product_brand_thumbnail_id'] ) );
		}
	}

	public function register_taxonomy() {
		$general = _x( 'Brands', 'taxonomy general name', $this->text_domain );
		$singular = _x( 'Brand', 'taxonomy singular name', $this->text_domain );
		$labels = array(
			'name'              => $general,
			'singular_name'     => $singular,
			'search_items'      => sprintf(__( 'Search %s', $this->text_domain ), $general ),
			'all_items'         => sprintf(__( 'All %s', $this->text_domain ), $general),
			'parent_item'       => sprintf(__( 'Parent %s', $this->text_domain ), $singular ),
			'parent_item_colon' => sprintf(__( 'Parent %s:', $this->text_domain ), $singular ),
			'edit_item'         => sprintf(__( 'Edit %s', $this->text_domain ), $singular ),
			'update_item'       => sprintf(__( 'Update %s', $this->text_domain ), $singular ),
			'add_new_item'      => sprintf(__( 'Add New %s', $this->text_domain ), $singular ),
			'new_item_name'     => sprintf(__( 'New %s Name', $this->text_domain ), $singular ),
			'menu_name'         => $singular,
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'			=> array(
				 'slug' => 'product-brands',
				 'hierarchical' => true
			 ),
			'capabilities' => array(
				'manage_terms' => 'manage_product_terms',
				'edit_terms'   => 'edit_product_terms',
				'delete_terms' => 'delete_product_terms',
				'assign_terms' => 'assign_product_terms'
			 )
		);
	
		$post_types = array( 'product' );

		register_taxonomy( $this->taxonomy_brand, $post_types, $args );
	}
}
$brand = new brand();
$brand->init();