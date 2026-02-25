<?php
class FF_Modules_Manager_API {
    
    function install($module){

        $res = [
            'success' => false,
            'message' => '',
        ];

        $this->create_dirs();
        
        $download_success = $this->download($module['download_url']);

        if( !$download_success ) {
            $res['message'] = 'file download/extract failed';
            return $res;
        }

        $res['check'] = $this->check_module($module);

        $this->item_add($module);

        $res['message'] = 'module installed';
        
        return $res;
    }

    function uninstall($module){
        
        $this->item_remove($module);

        $dir_path = FF_MODULES_DIR.pathinfo($module['download_url'])['filename'];

        $this->remove_directory($dir_path);
        
        return [
            'success' => true,
            'message' => 'module uninstalled',
        ];
    }

    function activate($module){

        $res = [
            'success' => false,
            'message' => '',
        ];

        $check = $this->check_incompatibility($module);

        if( $check['valid'] ) {
            $this->item_update( $module['slug'], 'active', true );
            $res['success'] = true;
            $res['message'] = 'module activated';
        }
        else {
            $res['message'] = $check['message'];
        }
        
        return $res;
    }

    function deactivate($module){
        
        $this->item_update( $module['slug'], 'active', false );

        return [
            'success' => true,
            'message' => 'module deactivated',
        ];
    }

    function create_dirs(){
        if( is_dir( FF_MODULES_DIR ) ) return;
        mkdir(FF_MODULES_DIR, 0755, true);
        mkdir(FF_MODULES_DIR.'temp', 0755, true);
    }

    function item_add($item){

        $modules = $this->get_site_modules();
        
        foreach( $modules as $module ) {
            if( $module['slug'] === $item['slug'] ) return; // already added
        }

        $item['active'] = false;
        $modules[] = $item;

        update_option('ff_modules_manager', $modules);
    }

    function item_remove($item){

        $updated_modules = [];

        foreach( $this->get_site_modules() as $module ) {
            if( $module['slug'] == $item['slug'] ) continue; 
            $updated_modules[] = $module;
        }

        update_option('ff_modules_manager', $updated_modules);
    }

    function item_update( $slug, $key, $value ){
        $modules = get_option('ff_modules_manager');
        $updated_modules = [];
        foreach( $modules as $module ) {
            if( $module['slug'] == $slug ) {
                $module[$key] = $value;
            }
            $updated_modules[] = $module;
        }
        update_option('ff_modules_manager', $updated_modules);
    }

    function check_incompatibility($item){
        $data = [
            'valid' => true,
            'message' => '',
        ];

        $classes_check = [
            'ff-ajax' => 'FF_Ajax',
            'ff-instagram' => 'FF_Instagram',
            'ff-au-post-integration' => 'FF_AU_Post',
            'ff-import-export' => '\FFIE\Import',
            'ff-thumbnail-rebuild' => 'FF_Thumbnail_Rebuild',
            'ff-reviews-schema' => 'FF_Reviews_Schema',
            'ff-quiz' => 'FFQuizFrontend',
        ];

        if( isset( $classes_check[$item['slug']] ) ) {
            $class_name = $classes_check[$item['slug']];
            if( class_exists( $class_name ) ) {
                return [
                    'valid' => false,
                    'message' => $class_name . ' class already exists. Please disable existing plugin or legacy code.',
                ];
            }
        }

        return $data;
    }   


    function download($file_url) {
        
        $file_path = FF_MODULES_DIR .'temp/'.pathinfo($file_url)['basename'];

        $success = file_put_contents($file_path, fopen($file_url.'?t='.time(), 'r'));
        if( !$success ) return false;
        
        WP_Filesystem();
        $extract_res = unzip_file($file_path, FF_MODULES_DIR);
        
        if( !is_bool($extract_res) && $extract_res->errors ) {
            
            $zip = new \ZipArchive();
            if ($zip && $zip->open($file_path) === TRUE) {
                $zip->extractTo(FF_MODULES_DIR);
                $zip->close();
            } else {
                // extract failed
                return false;
            }
        
        }

        // delete zip file after extract
        unlink($file_path);
        
        return $success;
    }

    function get_site_modules(){
        $modules = get_option('ff_modules_manager');
        if( !$modules ) $modules = [];
        return $modules;
    }

    function remove_directory($src) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    $this->remove_directory($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }

    function check_module($module){
        ob_start();
        try {
            $file = FF_MODULES_DIR.$module['slug'].'/'.$module['slug'].'.php';
            include_once $file;
            pre_debug('success');
        }
        catch (Error $e) {
            pre_debug('error');
            return $e;
        }
        return ob_get_clean();
    }
}