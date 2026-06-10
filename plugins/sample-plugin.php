<?php
/*
Plugin Name: Sample SEO Plugin
Description: A sample plugin to demonstrate extensibility.
*/
// You can hook into actions or add global variables here.
// For example, adding custom tracking codes:
$extra_head = ($extra_head ?? '') . "\n<!-- Sample Plugin Loaded -->\n";
