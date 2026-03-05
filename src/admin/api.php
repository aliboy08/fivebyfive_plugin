<?php
class FF_Plugin_Admin_API {

    function check_updates(){
       
        $request = wp_remote_get('https://devlibrary2021.wpengine.com/fivebyfive/data.json?t='.time());

        $repo_data = json_decode($request['body'], true);

        $site_data = file_get_contents(FF_PLUGIN_DIR.'/data.json');

        return [
            'repo_data' => json_decode($request['body'], true),
            'site_data' => json_decode($site_data, true),
        ];
    }
    
    function update_dist($payload){
        
        $temp_dir = FF_PLUGIN_DIR.'/temp';
        if( !is_dir($temp_dir) ) {
            mkdir($temp_dir, 0755, true);
        }
        
        $success = $this->download_dist();
        if( $success ) {
            $this->update_dist_data($payload['repo_data']['dist_version']);
        }
        
        return [
            'update_dist' => $payload,
            'success' => $success,
        ];
    }

    function update_dist_data($version){
        $file_path = FF_PLUGIN_DIR.'/data.json';
        $data = json_decode(file_get_contents($file_path), true);
        $data['dist_version'] = $version;
        file_put_contents($file_path, json_encode($data));
    }

    function download_dist() {

        $file_url = 'https://devlibrary2021.wpengine.com/fivebyfive/dist.zip';

        $temp_path = FF_PLUGIN_DIR.'/temp';
        $extract_path = FF_PLUGIN_DIR;
        
        $file_path = $temp_path.'/'.pathinfo($file_url)['basename'];
        
        $success = file_put_contents($file_path, fopen($file_url.'?t='.time(), 'r'));
        
        if( !$success ) return false;

        $this->remove_directory($extract_path.'/dist');
        
        WP_Filesystem();
        $extract_res = unzip_file($file_path, $extract_path);
        
        if( !is_bool($extract_res) && $extract_res->errors ) {
            $zip = new \ZipArchive();
            if ($zip && $zip->open($file_path) === TRUE) {
                $zip->extractTo($extract_path);
                $zip->close();
            } else {
                // extract failed
                return false;
            }
        }

        // delete zip file after extract
        unlink($file_path);
        
        return true;
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

}