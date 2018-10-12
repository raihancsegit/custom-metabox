<?php
/*
Plugin Name: Smart coder Meta Box
Author: Raihan Islam
Version:1.0.0
 */
function register_metabox()
{
    add_meta_box('all_page_id', 'Page Metabox', 'our_page_metabox', 'page', 'normal', 'high');
    add_meta_box('all_post_id', 'Post Metabox', 'our_post_metabox', 'post', 'side', 'low');
}
add_action('add_meta_boxes', 'register_metabox');

function codex_custom_init()
{
    $args = array(
        'public' => true,
        'label' => 'Books',
        'supports' => array('title', 'editor', 'thumbnail'),
    );
    register_post_type('book', $args);
}
add_action('init', 'codex_custom_init');

function book_custom_meta()
{
    add_meta_box('all_book_id', 'Book Metabox', 'our_book_metabox', 'book', 'normal', 'low');
    add_meta_box('all_book_author', 'Book Author', 'our_book_metabox_author', 'book', 'side', 'high');
}
add_action('add_meta_boxes_book', 'book_custom_meta');
function deshbord_setup()
{
    add_meta_box('all_deshbord_id', 'deshbord Metabox', 'our_deshbord_metabox', 'dashboard', 'normal', 'high');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // Right Now
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Recent Comments
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); // Incoming Links
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal'); // Plugins
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // Quick Press
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side'); // Recent Drafts
    remove_meta_box('dashboard_primary', 'dashboard', 'side'); // WordPress blog
    remove_meta_box('dashboard_secondary', 'dashboard', 'side'); // Other WordPress News
}
add_action('wp_dashboard_setup', 'deshbord_setup');

function our_book_metabox($post)
{

    ?>
        <div>
            <label for="">Name</label>
            <input type="text" name="name" placeholder="Enter Name" class="widefat" value="<?php echo get_post_meta($post->ID, 'name', true); ?>">
        </div>
    <?php
}
function save_data($post_id)
{
    update_post_meta($post_id, 'name', $_POST['name']);
}
add_action('save_post', 'save_data');

//book author
function our_book_metabox_author($post)
{
    wp_nonce_field(basename(__FILE__), 'book_author_nonce');
    $post_id = $post->ID;
    $author_id = get_post_meta($post_id, 'author_select', true);
    $all_author = get_users(array('role' => 'author'));
    ?>
    <label for="">Select Author</label>
     <select name="aselect" id="">
         <?php foreach ($all_author as $author) {
        $selected = "";
        if ($author_id == $author->data->ID) {
            $selected = 'selected="selected"';
        }

        //print_r($author);
        ?>
         <option value="<?php echo $author->data->ID; ?>" <?php echo $selected ?>><?php echo $author->data->display_name; ?></option>
         <?php }?>
     </select>
    <?php
}

function save_author_data($post_id, $post)
{
    if (!isset($_POST['book_author_nonce']) || !wp_verify_nonce($_POST['book_author_nonce'], basename(__FILE__))) {
    return $post_id;
}


    $book_slug = 'book';
    if ($book_slug != $post->post_type) {
        return $post_id;
    }
    $author_name = '';
    if (isset($_POST['aselect'])) {
        $author_name = sanitize_text_field($_POST['aselect']);

    } else {
        $author_name = '';

    }
    update_post_meta($post_id, 'author_select', $author_name);


}
add_action('save_post', 'save_author_data', 10, 2);
