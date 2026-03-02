<?php
namespace FF\Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Images_Accordion extends FF_Widget_Base {

	public function get_name() {
		return 'ff-images-accordion';
	}
	
	public function get_title() {
		return __( 'FF Images Accordion', 'ff' );
	}
	
	protected function register_controls() {
        
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'ff' ),
			]
		);
        
        $repeater = new \Elementor\Repeater();

        // $repeater->add_control(
		// 	'active',
		// 	[
		// 		'label' => esc_html__( 'Active', 'ff' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'label_on' => esc_html__( 'Yes', 'ff' ),
		// 		'label_off' => esc_html__( 'No', 'ff' ),
		// 	]
		// );

        $repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Image', 'ff' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

        $repeater->add_control(
			'heading', [
				'label' => esc_html__( 'Heading', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
			]
		);

        $repeater->add_control(
			'description', [
				'label' => esc_html__( 'Description', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
			]
		);

        $repeater->add_control(
			'button_text', [
				'label' => esc_html__( 'Button Text', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

        $repeater->add_control(
			'link', [
				'label' => esc_html__( 'Link', 'ff' ),
				'type' => \Elementor\Controls_Manager::URL,
                // 'options' => [ 'url', 'is_external', 'nofollow' ],
				'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
			]
		);
        
        $this->add_control(
			'items',
			[
				'label' => esc_html__( 'Items', 'ff' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ heading }}}',
			]
		);

        $this->add_control(
			'mode',
			[
				'label' => __( 'Mode', 'ff' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'click',
				'options' => [
                    'click' => 'Click',
                    'hover' => 'Hover',
                ],
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

        $this->add_control(
			'heading_html_tag',
			[
				'label' => __( 'Heading HTML Tag', 'ff' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => ff_get('heading_tag_options'),
			]
		);

        $this->add_control(
			'height',
			[
				'label' => __( 'Height (px)', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 620,
			]
		);

        $this->add_control(
			'min_width',
			[
				'label' => __( 'Min Width (px)', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 1000,
			]
		);

        $this->add_control(
			'accordion_animation_duration',
			[
				'label' => __( 'Animation Duration (ms)', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 800,
			]
		);

        // $this->add_control(
		// 	'item_template',
		// 	[
		// 		'label' => __( 'Item Template', 'fivebyfive' ),
		// 		'type' => \Elementor\Controls_Manager::SELECT,
        //         'options' => [
		// 			'' => 'Default',
		// 			// 'item-template-1' => 'item-template-1',
		// 		]
		// 	]
		// );

		$this->end_controls_section();
	}
	
	protected function render() {
        
		$settings = $this->get_settings_for_display();

        $this->inline_styles($settings);

        $item_template = 'item-template-default.php';
        // if( $settings['item_template'] ) {
        //     $item_template = $settings['item_template'];
        // }
        
        $heading_tag = $settings['heading_html_tag'] ? $settings['heading_html_tag'] : 'h4';

        $widget_settings = ff_json_encode([
            'mode' => $settings['mode'],
            'animation_duration' => $settings['accordion_animation_duration'],
        ]);
        
        echo '<div class="'. $this->get_name() .'" id="widget_'. $this->get_id() .'" data-widget_settings="'. $widget_settings .'">';
        
        $i = 0;
        foreach( $settings['items'] as $item ) {
            $i++;
            $is_current = $i == 1;
            include $item_template;
        }

        echo '</div>';
        
        ff_elementor_load_asset_dist('images_accordion');
        // ff_elementor_load_asset('images_accordion');
	}

    function inline_styles($settings){
        if( !$settings['height'] && !$settings['min_width'] ) return;
        
        $selector = '#widget_'. $this->get_id();
        
        echo '<style>';

        if( $settings['height'] ) {
            echo $selector .' {
                height: '. $settings['height'] .'px;
            }';
        }

        if( $settings['min_width'] ) {
            echo $selector .' .item_inner {
                min-width: '. $settings['min_width'] .'px;
            }';
        }

        echo '</style>';
    }
}

return new Images_Accordion();