<?php

namespace WooCustomFields\Admin;

defined( 'ABSPATH' ) || exit;

class Product_Fields {

	/**
	 * Array of custom pricing fields
	 *
	 * @since 1.0.0
	 */
	public $pricing_fields;

	/**
	 * Array of custom stock fields
	 *
	 * @since 1.0.0
	 */
	public $stock_fields;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->pricing_fields = $this->get_pricing_fields();
		$this->stock_fields   = $this->get_stock_fields();

		$this->hooks();

	}

	/**
	 * Register the hooks
	 *
	 * @since 1.0.0
	 */
	private function hooks() {

		// ACTIONS
		add_action( 'woocommerce_product_options_pricing', array( $this, 'add_product_fields_pricing' ), 10, 2 ); // parent :: general => pricing
		add_action( 'woocommerce_product_options_stock_fields', array( $this, 'add_product_fields_stock' ), 10, 2 ); // parent :: inventory => stock

		add_action( 'woocommerce_variation_options_pricing', array( $this, 'add_variation_fields_pricing' ), 10, 3 ); // variations :: pricing
		add_action( 'woocommerce_variation_options_inventory', array( $this, 'add_variation_fields_stock' ), 10, 3 ); // variations :: stock

		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_fields' ), 10, 2 ); // save product
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_product_variation_fields' ), 10, 2 ); // save variations

	}

	/**
	 * GET pricing fields
	 *
	 * @since 1.0.0
	 */
	public function get_pricing_fields() {

		$fields = array(
			array(
				'id'          => '_product_cost',
				'label'       => __( 'Product Cost', 'agentur_allison' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'description' => __( 'Not displayed in shop', 'agentur_allison' ),
			),
			/* add further pricing fields as needed */
		);

		return $fields;

	}

	/**
	 * GET stock fields
	 *
	 * @since 1.0.0
	 */
	public function get_stock_fields() {

		$fields = array(
			array(
				'id'          => '_new_stock_information',
				'label'       => __( 'New Stock Information', 'agentur_allison' ),
				'description' => __( 'Text displayed on product page', 'agentur_allison' ),
			),
			/* add further stock fields as needed */
		);

		return $fields;

	}

	/**
	 * ADD product fields
	 *
	 * @since 1.0.0
	 */
	public function add_product_fields( $fields, $product ) {

		foreach( $fields as $field ) {

			$args = array(
				'id'    => $field['id'],
				'label' => $field['label'],
				'value' => $this->get_product_field_value( $product, $field['id'] ),
			);

			if( isset( $field['description'] ) ) {
				$args['description'] = $field['description'];
				$args['desc_tip']    = true;
			}

			woocommerce_wp_text_input( $args );

		}

	}

	/**
	 * ADD product pricing fields
	 *
	 * @since 1.0.0
	 */
	public function add_product_fields_pricing( ) {

		global $product_object;

		$this->add_product_fields( $this->pricing_fields, $product_object );

	}

	/**
	 * ADD product stock fields
	 *
	 * @since 1.0.0
	 */
	public function add_product_fields_stock( ) {

		global $product_object;

		$this->add_product_fields( $this->stock_fields, $product_object );

	}

	/**
	* ADD variation fields
	*
	* @since 1.0.0
	*/
	public function add_variation_fields( $fields, $variation_product, $loop ) {

		foreach( $fields as $field ) {

			$args = array(
				'id'            => $field['id'] . '[' . $loop . ']',
				'label'         => $field['label'],
				'value'         => $this->get_product_field_value( $variation_product, $field['id'] ),
				'wrapper_class' => 'form-row form-row-full',
			);

			if( isset( $field['description'] ) ) {
				$args['description'] = $field['description'];
				$args['desc_tip']    = true;
			}

			woocommerce_wp_text_input( $args );

		}

	}

	/**
	 * ADD variations pricing fields
	 *
	 * @since 1.0.0
	 */
	public function add_variation_fields_pricing( $loop, $variation_data, $variation ) {

		$variation_product = wc_get_product( $variation->ID );

		$this->add_variation_fields( $this->pricing_fields, $variation_product, $loop );

	}

	/**
	 * ADD variations stock fields
	 *
	 * @since 1.0.0
	 */
	public function add_variation_fields_stock( $loop, $variation_data, $variation ) {

		$variation_product = wc_get_product( $variation->ID );

		$this->add_variation_fields( $this->stock_fields, $variation_product, $loop );

	}


	/**
	 * SAVE product fields
	 *
	 * @since 1.0.0
	 */
	public function save_product_fields( $post_id, $post ) {

		$product = wc_get_product( $post_id );
		$fields  = array_merge( $this->pricing_fields, $this->stock_fields );

		foreach( $fields as $field ) {

			$meta_value = isset( $_POST[ $field['id'] ] ) ? sanitize_text_field( $_POST[ $field['id'] ] ) : '';

			$product->update_meta_data( sanitize_key( $field['id'] ), $meta_value );

		}

		$product->save_meta_data();

	}

	/**
	 * SAVE product variation fields
	 *
	 * @since 1.0.0
	 */
	public function save_product_variation_fields( $variation_id, $i ) {

		$variation_product = wc_get_product( $variation_id );
		$fields            = array_merge( $this->pricing_fields, $this->stock_fields );

		foreach( $fields as $field ) {

			$meta_value = isset( $_POST[$field['id']][$i] ) ? sanitize_text_field( $_POST[$field['id']][$i] ) : '';

			$variation_product->update_meta_data( sanitize_key( $field['id'] ), $meta_value );

		}

		$variation_product->save_meta_data();

	}

	/**
	 * GET product field value
	 * Wrapper for get_meta()
	 *
	 * @since 1.0.0
	 */
	public function get_product_field_value( $product, $id ) {

		return $product->get_meta( sanitize_key( $id ) );

	}

}

new Product_Fields();
