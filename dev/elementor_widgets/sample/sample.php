<?php
namespace FF\Elementor_Widgets;

class FF_Sample extends FF_Widget_Base {

    public function get_name() {
		return 'sample';
    }
    
	public function get_title() {
		return __( 'Sample', 'fivebyfive' );
    }

}

return new FF_Sample();