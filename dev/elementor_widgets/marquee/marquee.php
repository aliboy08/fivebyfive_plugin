<?php
namespace FF\Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) exit;

class Marquee extends FF_Widget_Base {
    
	public function get_name() {
		return 'ff-marquee';
    }
    
	public function get_title() {
		return __( 'FF Marquee', 'ff' );
    }
    
	protected function register_controls() {

		// Content Tab Settings
   		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'ff' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$repeater = new \Elementor\Repeater();
        
		$repeater->add_control(
			'text_1', [
				'label' => __( 'Text 1', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

        $repeater->add_control(
			'text_2', [
				'label' => __( 'Text 2', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'ff' ),
				'type' => \Elementor\Controls_Manager::URL,
                'label_block' => true,
                'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'items',
			[
				'label' => __( 'Items', 'ff' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ text_1 }}}',
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => __( 'Speed (ms)', 'ff' ),
				'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __( 'Bigger number = slower', 'ff' ),
                'placeholder' => '2400',
			]
		);

        // $this->add_control(
		// 	'item_template',
		// 	[
		// 		'label' => __( 'Item Template', 'ff' ),
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

        if( !isset($settings['items']) || !$settings['items'] ) return;
        
        $item_template = 'item-template-default.php';

        $marquee_settings = ff_json_encode($this->get_marquee_settings($settings));
        
		echo '<div class="ff_marquee" data-settings="'. $marquee_settings .'">';
            echo '<div class="items">';
                foreach( $settings['items'] as $item ) {
                    include $item_template;
                }
            echo '</div>';
		echo '</div>';

        ff_elementor_load_asset_dist('marquee');
        // ff_elementor_load_asset('marquee');
	}

    function get_marquee_settings($settings){

        $marquee_settings = [
            'speed' => 2400,
        ];

        if( $settings['speed'] ?? false ) {
            $marquee_settings['speed'] = $settings['speed'];
        }

        return $marquee_settings;
    }
    
}

// \Elementor\Plugin::instance()->widgets_manager->register(new Marquee());
return new Marquee();