<?php
include_once FF_PLUGIN_DIR . '/src/manager/api_base.php';

class FF_Plugin_Elementor_Widgets_API extends FF_Plugin_Base_API {

    public $data_key = 'ff_plugin/elementor_widgets';
    public $dir = '';
    public $repo_endpoint = 'https://devlibrary2021.wpengine.com/fivebyfive/elementor_widgets/data.json';

    function get_items($payload){
        
        $repo_items = $this->get_repo_items($payload);

        $site_items = $this->get_site_items();

        $items = [];

        foreach( $repo_items as $repo_item ) {
            
            $item = $repo_item;

            $item['active'] = false;
            $item['installed'] = false;

            if( $site_items ) {
                foreach( $site_items as $site_item ) {
                    if( $item['slug'] === $site_item['slug'] ) {
                        $item['installed'] = true;
                        $item['active'] = $site_item['active'];
                        break;
                    }
                }
            }
            
            $items[] = $item;
        }

        return $items;
    }

    function install($payload){

        $item = $payload['item'];

        $this->update_dir();
        
        $this->create_dirs();
        
        $download_success = $this->download($item['file_url']);

        if( !$download_success ) {
            return [
                'error' => 'file download/extract failed',
            ];
        }

        $this->item_add($item);
        
        return [
            'dir' => $this->dir,
            'message' => 'widget installed',
        ];
    }

    function uninstall($payload){

        $item = $payload['item'];

        $this->update_dir();
        
        $this->item_remove($item);

        $dir_path = $this->dir.pathinfo($item['file_url'])['filename'];

        $this->remove_directory($dir_path);
        
        return [
            'message' => 'widget uninstalled',
        ];
    }

    function activate($payload){

        $item = $payload['item'];
        
        $item['active'] = true;

        $this->item_update( $item );
        
        return [
            'message' => 'widget activated'
        ];
    }

    function deactivate($payload){

        $item = $payload['item'];

        $item['active'] = false;
        
        $this->item_update( $item );

        return [
            'message' => 'widget deactivated',
        ];
    }

    function update_dir(){
        if( !$this->dir ) {
            $this->dir = get_stylesheet_directory().'/src/elementor/custom_widgets/';
        }
    }

}