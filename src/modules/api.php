<?php
include_once FF_PLUGIN_DIR . '/src/manager/api_base.php';

class FF_Plugin_Modules_API extends FF_Plugin_Base_API {

    public $data_key = 'ff_plugin/modules';
    public $dir = FF_MODULES_DIR;
    public $repo_endpoint = 'https://devlibrary2021.wpengine.com/fivebyfive/modules/modules.json';

    function get_items($payload){

        $site_modules = $this->get_site_items();

        $items = [];
        foreach( $this->get_repo_items($payload) as $module ) {
            
            $module['active'] = false;
            $module['installed'] = false;
            $module['outdated'] = false;

            if( $site_modules ) {
                foreach( $site_modules as $site_module ) {
                    if( $module['slug'] === $site_module['slug'] ) {
                        $module['installed'] = true;
                        $module['active'] = $site_module['active'];
                        $module['outdated'] = $module['version'] != $site_module['version'];
                        $module['old_version'] = $site_module['version'];
                        break;
                    }
                }
            }
            
            $items[] = $module;
        }

        return $items;
    }

    function install($payload){

        $item = $payload['item'];

        $this->create_dirs();
        
        $download_success = $this->download($item['file_url']);

        if( !$download_success ) {
            return [
                'error' => 'file download/extract failed',
            ];
        }

        $this->item_add($item);

        return [
            'message' => 'module installed',
        ];
    }

    function uninstall($payload){

        $item = $payload['item'];
        
        $this->item_remove($item);

        $dir_path = FF_MODULES_DIR.pathinfo($item['file_url'])['filename'];

        $this->remove_directory($dir_path);
        
        return [
            'message' => 'module uninstalled',
        ];
    }

    function activate($payload){

        $item = $payload['item'];

        $item['active'] = true;
        
        $this->item_update( $item );
        
        return [
            'message' => 'module activated',
        ];
    }

    function deactivate($payload){

        $item = $payload['item'];

        $item['active'] = false;
        
        $this->item_update( $item );

        return [
            'message' => 'module deactivated',
        ];
    }

    function update($payload){

        $module = $payload['item'];
        
        $download_success = $this->download($module['file_url']);

        if( !$download_success ) {
            return [
                'error' => 'file download/extract failed',
            ];
        }

        $site_module = $this->get_item($module['slug']);
        $site_module['version'] = $module['version'];

        $this->item_update($site_module);
        
        return [
            'message' => 'module updated',
        ];
    }

}