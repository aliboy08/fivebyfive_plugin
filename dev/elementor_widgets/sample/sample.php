<?php
namespace FF\Elementor_Widgets;

class FF_Sample extends FF_Widget_Base {

    public function get_name() {
		return 'sample';
    }
    
	public function get_title() {
		return __( 'Sample', 'fivebyfive' );
    }

    protected function render() {
        echo '<div class="sample_widget">';
            echo 'SAMPLE WIDGET';
        echo '</div>';

        ff_elementor_load_asset_dist('sample');
        // ff_elementor_load_asset('sample');
    }

}

return new FF_Sample();