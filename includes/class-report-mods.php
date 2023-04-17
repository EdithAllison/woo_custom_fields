<?php

namespace WooCustomFields;

defined( 'ABSPATH' ) || exit;

class ReportMods {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since	 1.0.0
	 */
	public function __construct() {

		$this->hooks();

	}

	/**
	 * Register the hooks
	 *
	 * @since 0.1.0
	 */
	private function hooks() {

		/* Query Args */
		add_filter( 'woocommerce_analytics_products_query_args', array( $this, 'apply_products_cost_calcs_args' ) );
		add_filter( 'woocommerce_analytics_products_stats_query_args',  array( $this, 'apply_products_cost_calcs_args' ) );

		/* Join Clause */
		add_filter( 'woocommerce_analytics_clauses_join_products_subquery', array( $this, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_products_stats_total', array( $this, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_products_stats_interval', array( $this, 'add_join_subquery' ) );

		/* Select Clause */
		add_filter( 'woocommerce_analytics_clauses_select_products_subquery', array( $this, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_products_stats_total', array( $this, 'add_select_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_products_stats_interval', array( $this, 'add_select_subquery' ) );

		add_filter( 'woocommerce_analytics_clauses_select_products', array( $this, 'add_select_query' ) );

	}

	/**
	 * SQL QUERY
	 */
	public function apply_products_cost_calcs_args( $args ) {

		$cost   = '0';
		$margin = '0';

		if ( isset( $_GET['cost'] ) ) {
			$landed_cost = sanitize_text_field( wp_unslash( $_GET['cost'] ) );
		}

		if ( isset( $_GET['margin'] ) ) {
			$margin = sanitize_text_field( wp_unslash( $_GET['margin'] ) );
		}

		$args['cost']	= $cost;
		$args['margin'] = $margin;

		return $args;
	}

	/**
	 * Add a JOIN clause.
	 *
	 * @param array $clauses an array of JOIN query strings.
	 * @return array augmented clauses.
	 */
	public function add_join_subquery( $clauses ) {

		global $wpdb;

		$clauses[] = "LEFT JOIN {$wpdb->prefix}woo_custom_fields ON {$wpdb->prefix}wc_order_product_lookup.order_item_id = {$wpdb->prefix}woo_custom_fields.order_item_id";

		return $clauses;
	}


	/**
	 * Add a SELECT clause.
	 *
	 * @param array $clauses an array of WHERE query strings.
	 * @return array augmented clauses.
	 */
	public function add_select_subquery( $clauses ) {

		global $wpdb;

		$clauses[] = ", SUM({$wpdb->prefix}woo_custom_fields.cost) AS cost, SUM({$wpdb->prefix}woo_custom_fields.margin) AS margin";

		return $clauses;
	}

	/**
	 * Add a SELECT clause.
	 *
	 * applies to single product filter
	 * @return array augmented clauses.
	 */
	public function add_select_query( $clauses ) {

		$clauses[] = ", cost, margin";

		return $clauses;
	}


}
