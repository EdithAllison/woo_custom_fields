<?php

namespace WooCustomFields\Admin;

defined( 'ABSPATH' ) || exit;

class CustomFields {

	/**
	 * Array of custom pricing fields
	 *
	 * @since 0.1.0
	 */
	public $pricing_fields;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->pricing_fields = $this->get_pricing_fields();

		$this->hooks();

	}

	/**
	 * Register the hooks
	 *
	 * @since 0.1.0
	 */
	private function hooks() {

		add_action( 'woocommerce_product_options_pricing', array( $this, 'add_product_fields_pricing' ), 10, 2 ); // parent :: general => pricing
		add_action( 'woocommerce_variation_options_pricing', array( $this, 'add_variation_fields_pricing' ), 10, 3 ); // variations :: pricing
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_fields' ), 10, 2 ); // save product
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_product_variation_fields' ), 10, 2 ); // save variations

	}

	/**
	 * GET pricing fields
	 *
	 * @since 0.1.0
	 */
	public function get_pricing_fields() {

		$fields = array(
			array(
				'id'          => '_product_cost',
				'label'       => __( 'Product Cost', 'woo_custom_fields' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'description' => __( 'Not displayed in shop', 'woo_custom_fields' ),
			),
			/* add further pricing fields as needed */
		);

		return $fields;

	}

	/**
	 * ADD product pricing fields
	 *
	 * @since 0.1.0
	 */
	public function add_product_fields_pricing( ) {

		global $product_object;

		foreach( $this->pricing_fields as $field ) {

			$args = array(
				'id'    => $field['id'],
				'label' => $field['label'],
				'value' => $this->get_product_field_value( $product_object, $field['id'] ),
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
	 * @since 0.1.0
	 */
	public function add_variation_fields_pricing( $loop, $variation_data, $variation ) {

		$variation_product = wc_get_product( $variation->ID );

		foreach( $this->pricing_fields as $field ) {

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
	 * SAVE product fields
	 *
	 * @since 0.1.0
	 */
	public function save_product_fields( $post_id, $post ) {

		$product = wc_get_product( $post_id );

		foreach( $this->pricing_fields as $field ) {

			$meta_value = isset( $_POST[ $field['id'] ] ) ? sanitize_text_field( $_POST[ $field['id'] ] ) : '';

			$product->update_meta_data( sanitize_key( $field['id'] ), $meta_value );

		}

		$product->save_meta_data();

	}

	/**
	 * SAVE product variation fields
	 *
	 * @since 0.1.0
	 */
	public function save_product_variation_fields( $variation_id, $i ) {

		$variation_product = wc_get_product( $variation_id );

		foreach( $this->pricing_fields as $field ) {

			$meta_value = isset( $_POST[$field['id']][$i] ) ? sanitize_text_field( $_POST[$field['id']][$i] ) : '';

			$variation_product->update_meta_data( sanitize_key( $field['id'] ), $meta_value );

		}

		$variation_product->save_meta_data();

	}

	/**
	 * GET product field value
	 * Wrapper for get_meta()
	 *
	 * @since 0.1.0
	 */
	public function get_product_field_value( $product, $id ) {

		return $product->get_meta( sanitize_key( $id ) );

	}

}
