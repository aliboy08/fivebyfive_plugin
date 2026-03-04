<?php
namespace FF\Plugin\Categories_Populate;

add_filter('ff/sub_menus', function($sub_menus){
    $sub_menus[] = [
        'slug' => 'categories_populate',
        'label' => 'Categories Populate',
        'render' => function(){
            ff_plugin_admin_scripts();
            admin_page();
            ff_plugin_load_asset('module_categories_populate');
        }
    ];
    return $sub_menus;
});

function admin_page(){
    $titles = $_POST['cp_titles'] ?? '';
    $categories = $_POST['cp_categories'] ?? '';
    ?>
    <h3>Categories Populate</h3>

    <div><b>Post Type</b>&nbsp;&nbsp;
        <select id="cp_post_type">
            <?php
            $post_types = get_post_types([
                'public'   => true
            ]);
            foreach( $post_types as $pt ) {
                echo '<option value="'. $pt .'">'. $pt .'</option>';
            }
            ?>
        </select>
    </div>

    <br/>

    <div><b>Taxonomy</b>&nbsp;&nbsp;
        <select id="cp_taxonomy">
            <?php
            foreach( get_taxonomies() as $taxonomy ) {
                echo '<option value="'. $taxonomy .'">'. $taxonomy .'</option>';
            }
            ?>
        </select>
    </div>

    <br/>

    <div><b>Items per batch</b>&nbsp;&nbsp;
        <input type="number" id="cp_num_per_batch" value=5>&nbsp;
        <span>Reduce if process is timing out</span>
    </div>

    <h4>Enter Data</h4>
    <div>
        <textarea id="cp_titles" placeholder="Titles" rows=10></textarea>
        <textarea id="cp_categories" placeholder="Categories / Terms" rows=10></textarea>
    </div>
    <br/>
    <button class="button-primary" id="cp_prepare_btn">Prepare Data</button>
    <div id="cp_result"></div>
    <?php
}

add_action('wp_ajax_ff_plugin_categories_populate_api', function(){
    $payload = json_decode(file_get_contents('php://input'), true);
    if ( ! wp_verify_nonce( $payload['nonce'], 'ff_plugin' ) ) die();
    include_once 'api.php';
    $api = new API();
    $action = $payload['action'];
    wp_send_json($api->$action($payload));
});
