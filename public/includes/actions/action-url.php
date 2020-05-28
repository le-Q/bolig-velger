<?php
class DrawAttention_URL_Action extends DrawAttention_Action {
	function add_action_fields( $group_details ) {
		if ( ! $this->is_active() ) {
			return $group_details;
		}
		
		$group_details['fields'][0]['fields']['action']['options'] = __( 'Go to URL', 'bolig-velger' );

		$group_details['fields'][0]['fields']['action-url-url'] = array(
			'name' => __('URL', 'bolig-velger' ),
			'id'   => 'action-url-url',
			'type' => 'text_url',
			'attributes' => array(
				'placeholder' => site_url( 'custom/url/' ),
				'data-action' => 'url',
			),
		);

		return $group_details;
	}	
}