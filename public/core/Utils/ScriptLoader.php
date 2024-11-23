<?php

class ScriptLoader{
    public function register(bool $createNonce, Script $script): void
    {
        wp_enqueue_script($script->handle, plugin_dir_url("")."micerule-tables/public/js/".$script->fileName.".js", $script->deps, $script->version, $script->args);

        if($createNonce){
            wp_localize_script($script->handle,'miceruleApi', array(
                'eventPostID' => get_the_ID(),
                'nonce'    => wp_create_nonce('wp_rest'),
            ));
        }
    }

    public function registerScripts(): void
    {
        require_once plugin_dir_path(dirname(__FILE__)).'Utils/scripts.php';
    }
}