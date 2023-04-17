/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { getSetting } from '@woocommerce/settings';
import { CurrencyFactory } from '@woocommerce/currency';

/**
 * Internal dependencies
 */
import './index.scss';

const CURRENCY = getSetting( 'currency' );

const { formatAmount, formatDecimal } = CurrencyFactory( CURRENCY );


/**
 * Cost
 */

const addTableColumnProducts = reportTableData => {

	if (
		reportTableData.endpoint !== 'products' ||
		! reportTableData.items ||
		! reportTableData.items.data ||
		! reportTableData.items.data.length
	) {
		return reportTableData;
	}

	const newHeaders = [
		...reportTableData.headers,
		{
			label: __( 'Cost', 'woo_custom_fields' ),
			key: 'cost',
			isNumeric: true,
		},
		{
			label: __( 'Margin', 'woo_custom_fields' ),
			key: 'margin',
			isNumeric: true,
		},
	];
	const newRows = reportTableData.rows.map( ( row, index ) => {
		const item = reportTableData.items.data[ index ];
		const newRow = [
			...row,
			{
				display: formatAmount( item.cost ),
				value: item.cost,
			},
			{
				display: formatAmount( item.margin ),
				value: item.margin,
			},
		];
		return newRow;
	} );


	const newTotals = {
		...reportTableData.totals,
		markup: reportTableData.totals.margin / reportTableData.totals.cost,
	};


	const newSummary = [
		...reportTableData.summary,
		{
			label: __( 'Cost', 'woo_custom_fields' ),
			value: formatAmount( newTotals.cost ),
		},
		{
			label: __( 'Margin', 'woo_custom_fields' ),
			value:  formatAmount( newTotals.margin ),
		},
	];


	reportTableData.headers = newHeaders;
	reportTableData.rows    = newRows;
	reportTableData.totals  = newTotals;
	reportTableData.summary = newSummary;

	return reportTableData;
};

addFilter( 'woocommerce_admin_report_table', 'costreport', addTableColumnProducts );
