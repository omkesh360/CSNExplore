<?php
require 'php/config.php';
$db = getDB();

$hp_settings_row = $db->fetchOne("SELECT content FROM about_contact WHERE section = 'homepage'");
$hp_settings = [];
if ($hp_settings_row && !empty($hp_settings_row['content'])) {
    $hp_settings = json_decode($hp_settings_row['content'], true);
}

// Update settings to include cars and stays with requested order
$hp_settings['section_order'] = ['cars', 'bikes', 'attractions', 'stays', 'restaurants', 'buses', 'blogs'];
$hp_settings['show_cars'] = true;
$hp_settings['show_stays'] = true;
$hp_settings['count_cars'] = 4;
$hp_settings['count_stays'] = 4;
$hp_settings['layout_cars'] = '4-col';
$hp_settings['layout_stays'] = '4-col';
$hp_settings['title_cars'] = 'Self Drive Cars';
$hp_settings['title_stays'] = 'Premium Stays';

$new_content = json_encode($hp_settings);

$db->query("UPDATE about_contact SET content = ? WHERE section = 'homepage'", [$new_content]);

echo "Database updated successfully with new homepage section order including Cars and Stays.\n";
