<?php
namespace FF\Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Before_After_Slider extends FF_Widget_Base {

	public function get_name() {
		return 'ff-before-after-slider';
	}
	
	public function get_title() {
		return __( 'FF Before After Slider', 'ff' );
	}
	
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'ff' ),
			]
		);

		$this->add_control(
			'before_image',
			[
				'label' => __( 'Before Image', 'ff' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

        $this->add_control(
			'after_image',
			[
				'label' => __( 'After Image', 'ff' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

        $this->add_control(
			'height',
			[
				'label' => __( 'Height', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 620,
			]
		);
        
        $this->add_control(
			'image_size',
			[
				'label' => __( 'Image Size', 'ff' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'full',
				'options' => ff_get('image_size_options'),
			]
		);

		$this->end_controls_section();
	}
	
	protected function render() {
        
		$settings = $this->get_settings_for_display();

        echo '<div class="ff_before_after_slider"'. $this->styles_attr($settings) .'>';
            echo '<div class="before_image">'. $this->get_before_image_html($settings) .'</div>';
            echo '<div class="after_image">'. $this->get_after_image_html($settings) .'</div>';
            echo '<div class="handle"><i class="handle_icon"></i></div>';
        echo '</div>';
        
        ff_elementor_load_asset_dist('before_after_slider');
        // ff_elementor_load_asset('before_after_slider');
	}

    private function styles_attr($settings){
        if( !$settings['height'] ) return '';
        return ' style="--height:'. $settings['height'].'px"';
    }

    private function get_before_image_html($settings){
        $html = '';
        if( $settings['before_image']['id'] ) {
            $html = wp_get_attachment_image($settings['before_image']['id'], $settings['image_size']);
        }
        return $html;
    }

    private function get_after_image_html($settings){
        $html = '';
        if( $settings['after_image']['id'] ) {
            $html = wp_get_attachment_image($settings['after_image']['id'], $settings['image_size']);
        }
        return $html;
    }
}

return new Before_After_Slider();