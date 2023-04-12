/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { Dropdown } from '@wordpress/components';
import * as Woo from '@woocommerce/components';
import { Fragment } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './index.scss';

const MyExamplePage = () => (
	<Fragment>
		<Woo.Section component="article">
			<Woo.SectionHeader title={ __( 'Search', 'woo-custom-fields' ) } />
			<Woo.Search
				type="products"
				placeholder="Search for something"
				selected={ [] }
				onChange={ ( items ) => setInlineSelect( items ) }
				inlineTags
			/>
		</Woo.Section>

		<Woo.Section component="article">
			<Woo.SectionHeader title={ __( 'Dropdown', 'woo-custom-fields' ) } />
			<Dropdown
				renderToggle={ ( { isOpen, onToggle } ) => (
					<Woo.DropdownButton
						onClick={ onToggle }
						isOpen={ isOpen }
						labels={ [ 'Dropdown' ] }
					/>
				) }
				renderContent={ () => <p>Dropdown content here</p> }
			/>
		</Woo.Section>

		<Woo.Section component="article">
			<Woo.SectionHeader title={ __( 'Pill shaped container', 'woo-custom-fields' ) } />
			<Woo.Pill className={ 'pill' }>
				{ __( 'Pill Shape Container', 'woo-custom-fields' ) }
			</Woo.Pill>
		</Woo.Section>

		<Woo.Section component="article">
			<Woo.SectionHeader title={ __( 'Spinner', 'woo-custom-fields' ) } />
			<Woo.H>I am a spinner!</Woo.H>
			<Woo.Spinner />
		</Woo.Section>

		<Woo.Section component="article">
			<Woo.SectionHeader title={ __( 'Datepicker', 'woo-custom-fields' ) } />
			<Woo.DatePicker
				text={ __( 'I am a datepicker!', 'woo-custom-fields' ) }
				dateFormat={ 'MM/DD/YYYY' }
			/>
		</Woo.Section>
	</Fragment>
);

addFilter( 'woocommerce_admin_pages_list', 'woo-custom-fields', ( pages ) => {
	pages.push( {
		container: MyExamplePage,
		path: '/woo-custom-fields',
		breadcrumbs: [ __( 'Woo Custom Fields', 'woo-custom-fields' ) ],
		navArgs: {
			id: 'woo_custom_fields',
		},
	} );

	return pages;
} );
