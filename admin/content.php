<?php
$admin_page  = 'content';
$admin_title = 'Page Content | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Content Management</h2>
            <p class="text-xs text-slate-500 font-medium">Manage website text, sections, and communication</p>
        </div>
    </div>
<!-- Tabs -->
<div class="flex gap-1 bg-white border border-slate-200 p-1 rounded-xl w-fit">
    <button onclick="switchSection('about')" data-section="about"
            class="content-tab px-4 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
        About Us
    </button>
    <button onclick="switchSection('contact')" data-section="contact"
            class="content-tab px-4 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
        Contact Info
    </button>
    <button onclick="switchSection('homepage')" data-section="homepage"
            class="content-tab px-4 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
        Homepage
    </button>
    <button onclick="switchSection('messages')" data-section="messages"
            class="content-tab px-4 py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-900 transition-all">
        Messages
    </button>
</div>

<!-- About section -->
<div id="section-about" class="space-y-4">
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
        <h2 class="text-base font-bold">About Us Content</h2>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Mission Statement</label>
            <textarea id="about-mission" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none resize-none"></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Vision</label>
            <textarea id="about-vision" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none resize-none"></textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">About Description</label>
            <textarea id="about-description" rows="4" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none resize-none"></textarea>
        </div>
        <div id="about-success" class="hidden text-sm text-green-700 bg-green-50 px-4 py-2 rounded-xl">Saved successfully!</div>
        <button onclick="saveAbout()" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">Save About Content</button>
    </div>
</div>

<!-- Contact section -->
<div id="section-contact" class="hidden space-y-4">
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
        <h2 class="text-base font-bold">Contact Information</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Phone</label>
                <input id="contact-phone" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                <input id="contact-email" type="email" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">WhatsApp</label>
                <input id="contact-whatsapp" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Hours</label>
                <input id="contact-hours" type="text" placeholder="Mon-Sat: 9am - 7pm" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none"/>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Address</label>
                <textarea id="contact-address" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none resize-none"></textarea>
            </div>
        </div>
        <div id="contact-success" class="hidden text-sm text-green-700 bg-green-50 px-4 py-2 rounded-xl">Saved successfully!</div>
        <button onclick="saveContact()" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">Save Contact Info</button>
    </div>
</div>

<!-- Homepage section -->
<div id="section-homepage" class="hidden space-y-4">

    <!-- Hero Text -->
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-sm font-bold">✦</span>
            <h2 class="text-base font-bold text-slate-900">Hero Section</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Hero Headline</label>
                <input id="hp-hero-headline" type="text" placeholder="Explore Your City Your Way" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Hero Subtext</label>
                <input id="hp-hero-subtext" type="text" placeholder="Stays, cars, bikes, restaurants..." class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"/>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-4">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 text-sm font-bold">✎</span>
            <h2 class="text-base font-bold text-slate-900">Page Content</h2>
            <span class="text-xs text-slate-400 ml-1">Text shown in homepage sections</span>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">City Intro / About Blurb <span class="text-slate-400 font-normal">(shown below hero)</span></label>
            <textarea id="hp-city-intro" rows="3" placeholder="Chhatrapati Sambhajinagar is a city of ancient wonders..." class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors resize-none"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Stats Bar — Stat 1 Label</label>
                <input id="hp-stat1-label" type="text" placeholder="e.g. 500+ Hotels" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Stats Bar — Stat 2 Label</label>
                <input id="hp-stat2-label" type="text" placeholder="e.g. 50+ Attractions" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Stats Bar — Stat 3 Label</label>
                <input id="hp-stat3-label" type="text" placeholder="e.g. 200+ Restaurants" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Stats Bar — Stat 4 Label</label>
                <input id="hp-stat4-label" type="text" placeholder="e.g. 10K+ Happy Travelers" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"/>
            </div>
        </div>
    </div>

    <!-- Marquee Bar -->
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center text-primary text-base">★</span>
                <h2 class="text-base font-bold text-slate-900">Marquee Bar</h2>
            </div>
            <span class="text-xs text-slate-400">One item per line · shown in the orange strip at top</span>
        </div>
        <textarea id="hp-marquee" rows="5" placeholder="★ 20% OFF on first heritage tour booking&#10;★ Verified guides for Ajanta &amp; Ellora&#10;★ Free cancellation on bike rentals&#10;★ 24/7 tourist support available" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors resize-none font-mono leading-relaxed"></textarea>
        <p class="text-xs text-slate-400">Each line becomes one marquee item. Use ★ prefix for style.</p>
    </div>

    <!-- Section Visibility + Titles + Controls -->
    <div class="bg-white rounded-2xl border border-slate-100 p-6 space-y-3">
        <div class="flex items-center gap-2 mb-2">
            <span class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 text-sm font-bold">☰</span>
            <h2 class="text-base font-bold text-slate-900">Sections</h2>
            <span class="text-xs text-slate-400 ml-1">Drag to reorder · toggle visibility · set title, count & layout</span>
        </div>
        <?php
        $hp_sections = [
            'cars'        => ['icon' => '🚗', 'default' => 'Self Drive Cars',    'desc' => 'Car rentals & self-drive',    'layouts' => ['4-col','3-col','2-col'], 'counts' => [4,6,8]],
            'bikes'       => ['icon' => '🏍️', 'default' => 'Quick Bike Rentals', 'desc' => 'Bikes & scooters for rent',    'layouts' => ['4-col','3-col','2-col'], 'counts' => [4,6,8]],
            'attractions' => ['icon' => '🏛️', 'default' => 'Ancient Marvels',    'desc' => 'Heritage sites & attractions', 'layouts' => ['4-col','3-col','2-col'], 'counts' => [4,6,8]],
            'stays'       => ['icon' => '🏨', 'default' => 'Premium Stays',      'desc' => 'Hotels & resorts',             'layouts' => ['4-col','3-col','2-col'], 'counts' => [4,6,8]],
            'restaurants' => ['icon' => '🍽️', 'default' => 'Taste the City',     'desc' => 'Restaurants & dining',         'layouts' => ['3-col','4-col','2-col'], 'counts' => [6,8,12]],
            'buses'       => ['icon' => '🚌', 'default' => 'Travel Your Way',    'desc' => 'Bus routes & operators',       'layouts' => ['2-col','3-col','list'],   'counts' => [2,4,6]],
            'blogs'       => ['icon' => '📝', 'default' => 'Travel Insights',    'desc' => 'Blog posts & travel guides',   'layouts' => ['3-col','2-col','4-col'], 'counts' => [3,4,6]],
        ];
        ?>
        <div id="hp-sections-list" class="space-y-2">
            <?php foreach ($hp_sections as $key => $sec): ?>
            <div class="hp-section-row rounded-xl border border-slate-100 hover:border-slate-200 transition-all bg-white" data-key="<?php echo $key; ?>">
                <!-- Row header -->
                <div class="flex items-center gap-3 p-3.5">
                    <span class="drag-handle cursor-grab text-slate-300 hover:text-slate-500 shrink-0 select-none" title="Drag to reorder">⠿</span>
                    <span class="text-xl shrink-0"><?php echo $sec['icon']; ?></span>
                    <div class="flex-1 min-w-0">
                        <input id="hp-title-<?php echo $key; ?>" type="text"
                               placeholder="<?php echo $sec['default']; ?>"
                               class="w-full text-sm font-semibold text-slate-800 bg-transparent border-b border-transparent hover:border-slate-200 focus:border-primary focus:outline-none py-0.5 transition-colors placeholder-slate-400"/>
                        <p class="text-xs text-slate-400 mt-0.5"><?php echo $sec['desc']; ?></p>
                    </div>
                    <!-- Expand toggle -->
                    <button type="button" onclick="toggleSectionOpts('<?php echo $key; ?>')"
                            class="text-slate-400 hover:text-primary transition-colors shrink-0 text-xs font-semibold px-2 py-1 rounded-lg hover:bg-slate-50">
                        ⚙ Options
                    </button>
                    <label class="relative shrink-0 cursor-pointer">
                        <input type="checkbox" id="hp-show-<?php echo $key; ?>" class="sr-only hp-toggle" checked
                               onchange="this.closest('.flex').style.opacity = this.checked ? '1' : '0.45'"/>
                        <div class="hp-track w-10 h-5 bg-slate-200 rounded-full transition-colors"></div>
                        <div class="hp-thumb absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                    </label>
                </div>
                <!-- Expandable options -->
                <div id="hp-opts-<?php echo $key; ?>" class="hidden border-t border-slate-50 px-4 pb-4 pt-3 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Cards to show</label>
                            <select id="hp-count-<?php echo $key; ?>"
                                    class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-sm focus:outline-none focus:border-primary">
                                <?php foreach ($sec['counts'] as $c): ?>
                                <option value="<?php echo $c; ?>"><?php echo $c; ?> cards</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Layout</label>
                            <select id="hp-layout-<?php echo $key; ?>"
                                    class="w-full border border-slate-200 rounded-lg px-2.5 py-1.5 text-sm focus:outline-none focus:border-primary">
                                <?php foreach ($sec['layouts'] as $l): ?>
                                <option value="<?php echo $l; ?>"><?php echo str_replace('-',' ', ucfirst($l)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Pick specific items -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-slate-500">Pick specific items <span class="text-slate-400 font-normal">(leave all unchecked to use count above)</span></label>
                            <button type="button" onclick="loadPickItems('<?php echo $key; ?>')"
                                    class="text-[11px] font-bold text-primary hover:underline">Load items</button>
                        </div>
                        <div id="hp-pick-list-<?php echo $key; ?>"
                             class="max-h-52 overflow-y-auto border border-slate-100 rounded-xl p-2 bg-slate-50 text-xs text-slate-400 italic">
                            Click "Load items" to browse
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="text-xs text-slate-400 pt-1">Drag rows to change the order sections appear on the homepage.</p>
    </div>

    <div id="homepage-success" class="hidden text-sm text-green-700 bg-green-50 border border-green-200 px-4 py-2.5 rounded-xl font-medium">✓ Homepage settings saved successfully!</div>
    <div id="homepage-error" class="hidden text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-2.5 rounded-xl font-medium">✗ Save failed. Please try again.</div>
    <button onclick="saveHomepage()" id="hp-save-btn" class="bg-primary text-white px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all shadow-sm">Save Homepage Settings</button>
</div>

<!-- Messages section -->
<div id="section-messages" class="hidden space-y-4">
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold">Contact Form Messages</h2>
            <span id="msg-count" class="text-xs text-slate-400"></span>
        </div>
        <div id="messages-list" class="divide-y divide-slate-50">
            <div class="text-center py-12 text-slate-400">Loading...</div>
        </div>
    </div>
</div>

</div>

<?php
$extra_js = '<script>
function toggleSectionOpts(key) {
    var el = document.getElementById("hp-opts-" + key);
    if (el) el.classList.toggle("hidden");
}

async function loadPickItems(key) {
    var container = document.getElementById("hp-pick-list-" + key);
    container.innerHTML = "<span class=\"text-slate-400 italic\">Loading...</span>";
    try {
        var res = await fetch("../php/api/hp_items.php?table=" + key);
        var items = await res.json();
        if (!items || !items.length) {
            container.innerHTML = "<span class=\"text-slate-400 italic\">No items found</span>";
            return;
        }
        // Collect currently checked IDs before re-rendering
        var checked = {};
        container.querySelectorAll("input[type=checkbox]").forEach(function(cb) {
            if (cb.checked) checked[cb.value] = true;
        });
        container.innerHTML = items.map(function(item) {
            var isChecked = checked[item.id] ? " checked" : "";
            var thumb = item.image
                ? "<img src=\"" + escHtml(item.image) + "\" class=\"w-8 h-8 rounded object-cover shrink-0\" onerror=\"this.style.display=\'none\'\" />"
                : "<span class=\"w-8 h-8 rounded bg-slate-200 flex items-center justify-center shrink-0 text-slate-400 text-xs\">?</span>";
            return "<label class=\"flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-white cursor-pointer\">" +
                "<input type=\"checkbox\" id=\"hp-pick-" + key + "-" + item.id + "\" value=\"" + item.id + "\"" + isChecked + " class=\"accent-orange-500 shrink-0\" />" +
                thumb +
                "<span class=\"text-slate-700 font-medium\">" + escHtml(item.name) + "</span>" +
                "<span class=\"text-slate-400 text-[10px] ml-auto\">" + escHtml(item.type || "") + "</span>" +
            "</label>";
        }).join("");
    } catch(e) {
        container.innerHTML = "<span class=\"text-red-400 italic\">Failed to load</span>";
    }
}

// Drag-to-reorder sections
(function(){
    var list, dragging;
    document.addEventListener("DOMContentLoaded", function(){
        list = document.getElementById("hp-sections-list");
        if (!list) return;
        list.addEventListener("dragstart", function(e){
            dragging = e.target.closest(".hp-section-row");
            if (dragging) { setTimeout(function(){ dragging.style.opacity="0.4"; }, 0); }
        });
        list.addEventListener("dragend", function(){
            if (dragging) { dragging.style.opacity="1"; dragging = null; }
        });
        list.addEventListener("dragover", function(e){
            e.preventDefault();
            var target = e.target.closest(".hp-section-row");
            if (target && target !== dragging) {
                var rect = target.getBoundingClientRect();
                var after = e.clientY > rect.top + rect.height / 2;
                list.insertBefore(dragging, after ? target.nextSibling : target);
            }
        });
        // Make rows draggable via handle
        list.querySelectorAll(".hp-section-row").forEach(function(row){
            row.setAttribute("draggable","true");
        });
    });
})();

function switchSection(s) {
    document.querySelectorAll(".content-tab").forEach(function(b){
        var active = b.dataset.section === s;
        b.classList.toggle("bg-primary", active);
        b.classList.toggle("text-white", active);
        b.classList.toggle("text-slate-500", !active);
    });
    ["about","contact","homepage","messages"].forEach(function(id){
        var el = document.getElementById("section-" + id);
        if (el) el.classList.toggle("hidden", id !== s);
    });
    if (s === "messages") loadMessages();
}

async function loadAbout() {
    var data = await api("../php/api/about_contact.php?section=about");
    if (!data) return;
    document.getElementById("about-mission").value     = data.mission || "";
    document.getElementById("about-vision").value      = data.vision || "";
    document.getElementById("about-description").value = data.description || "";
}

async function loadContact() {
    var data = await api("../php/api/about_contact.php?section=contact");
    if (!data) return;
    document.getElementById("contact-phone").value    = data.phone || "";
    document.getElementById("contact-email").value    = data.email || "";
    document.getElementById("contact-whatsapp").value = data.whatsapp || "";
    document.getElementById("contact-hours").value    = data.hours || "";
    document.getElementById("contact-address").value  = data.address || "";
}

async function loadHomepage() {
    var data = await api("../php/api/about_contact.php?section=homepage");
    if (!data) return;
    if (data.marquee !== undefined) document.getElementById("hp-marquee").value = data.marquee;
    if (data.hero_headline) document.getElementById("hp-hero-headline").value = data.hero_headline;
    if (data.hero_subtext)  document.getElementById("hp-hero-subtext").value  = data.hero_subtext;
    if (data.city_intro !== undefined) document.getElementById("hp-city-intro").value = data.city_intro;
    if (data.stat1_label !== undefined) document.getElementById("hp-stat1-label").value = data.stat1_label;
    if (data.stat2_label !== undefined) document.getElementById("hp-stat2-label").value = data.stat2_label;
    if (data.stat3_label !== undefined) document.getElementById("hp-stat3-label").value = data.stat3_label;
    if (data.stat4_label !== undefined) document.getElementById("hp-stat4-label").value = data.stat4_label;
    ["cars","bikes","attractions","stays","restaurants","buses","blogs"].forEach(function(k){
        var cb = document.getElementById("hp-show-" + k);
        var ti = document.getElementById("hp-title-" + k);
        var co = document.getElementById("hp-count-" + k);
        var la = document.getElementById("hp-layout-" + k);
        var checked = data["show_" + k] !== false;
        if (cb) { cb.checked = checked; updateToggle(cb); cb.closest(".flex").style.opacity = checked ? "1" : "0.45"; }
        if (ti && data["title_" + k]) ti.value = data["title_" + k];
        if (co && data["count_" + k]) co.value = data["count_" + k];
        if (la && data["layout_" + k]) la.value = data["layout_" + k];
        // Restore picks: load items then re-check saved IDs
        if (data["picks_" + k] && data["picks_" + k].length) {
            var savedPicks = data["picks_" + k];
            fetch("../php/api/hp_items.php?table=" + k)
                .then(function(r){ return r.json(); })
                .then(function(items) {
                    if (!items || !items.length) return;
                    var container = document.getElementById("hp-pick-list-" + k);
                    container.innerHTML = items.map(function(item) {
                        var isChecked = savedPicks.indexOf(item.id) !== -1 ? " checked" : "";
                        var thumb = item.image
                            ? "<img src=\"" + escHtml(item.image) + "\" class=\"w-8 h-8 rounded object-cover shrink-0\" onerror=\"this.style.display=\'none\'\" />"
                            : "<span class=\"w-8 h-8 rounded bg-slate-200 flex items-center justify-center shrink-0 text-slate-400 text-xs\">?</span>";
                        return "<label class=\"flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-white cursor-pointer\">" +
                            "<input type=\"checkbox\" id=\"hp-pick-" + k + "-" + item.id + "\" value=\"" + item.id + "\"" + isChecked + " class=\"accent-orange-500 shrink-0\" />" +
                            thumb +
                            "<span class=\"text-slate-700 font-medium\">" + escHtml(item.name) + "</span>" +
                            "<span class=\"text-slate-400 text-[10px] ml-auto\">" + escHtml(item.type || "") + "</span>" +
                        "</label>";
                    }).join("");
                });
        }
    });
    // Restore section order
    if (data.section_order && Array.isArray(data.section_order)) {
        var list = document.getElementById("hp-sections-list");
        data.section_order.forEach(function(key) {
            var row = list.querySelector("[data-key=\"" + key + "\"]");
            if (row) list.appendChild(row);
        });
    }
}

async function saveAbout() {
    var data = {
        mission:     document.getElementById("about-mission").value,
        vision:      document.getElementById("about-vision").value,
        description: document.getElementById("about-description").value
    };
    await api("../php/api/about_contact.php", { method: "PUT", body: JSON.stringify({ section: "about", data: data }) });
    var s = document.getElementById("about-success");
    s.classList.remove("hidden");
    setTimeout(function(){ s.classList.add("hidden"); }, 3000);
}

async function saveContact() {
    var data = {
        phone:    document.getElementById("contact-phone").value,
        email:    document.getElementById("contact-email").value,
        whatsapp: document.getElementById("contact-whatsapp").value,
        hours:    document.getElementById("contact-hours").value,
        address:  document.getElementById("contact-address").value
    };
    await api("../php/api/about_contact.php", { method: "PUT", body: JSON.stringify({ section: "contact", data: data }) });
    var s = document.getElementById("contact-success");
    s.classList.remove("hidden");
    setTimeout(function(){ s.classList.add("hidden"); }, 3000);
}

async function saveHomepage() {
    var btn = document.getElementById("hp-save-btn");
    btn.disabled = true; btn.textContent = "Saving...";
    var data = {
        marquee:       document.getElementById("hp-marquee").value,
        hero_headline: document.getElementById("hp-hero-headline").value,
        hero_subtext:  document.getElementById("hp-hero-subtext").value,
        city_intro:    document.getElementById("hp-city-intro").value,
        stat1_label:   document.getElementById("hp-stat1-label").value,
        stat2_label:   document.getElementById("hp-stat2-label").value,
        stat3_label:   document.getElementById("hp-stat3-label").value,
        stat4_label:   document.getElementById("hp-stat4-label").value
    };
    // Section order from DOM
    var order = Array.from(document.querySelectorAll("#hp-sections-list .hp-section-row")).map(function(r){ return r.dataset.key; });
    data.section_order = order;
    ["cars","bikes","attractions","stays","restaurants","buses","blogs"].forEach(function(k){
        var cb = document.getElementById("hp-show-" + k);
        var ti = document.getElementById("hp-title-" + k);
        var co = document.getElementById("hp-count-" + k);
        var la = document.getElementById("hp-layout-" + k);
        data["show_" + k]   = cb ? cb.checked : true;
        data["title_" + k]  = ti ? ti.value : "";
        data["count_" + k]  = co ? co.value : "";
        data["layout_" + k] = la ? la.value : "";
        // Collect picked IDs (empty array = use count/order)
        data["picks_" + k] = Array.from(document.querySelectorAll(
            "#hp-pick-list-" + k + " input[type=checkbox]:checked"
        )).map(function(el){ return parseInt(el.value, 10); });
    });
    try {
        var res = await api("../php/api/about_contact.php", { method: "PUT", body: JSON.stringify({ section: "homepage", data: data }) });
        var ok = document.getElementById("homepage-success");
        var er = document.getElementById("homepage-error");
        if (res && res.success) {
            ok.classList.remove("hidden"); er.classList.add("hidden");
            setTimeout(function(){ ok.classList.add("hidden"); }, 3000);
        } else {
            er.classList.remove("hidden"); ok.classList.add("hidden");
            setTimeout(function(){ er.classList.add("hidden"); }, 4000);
        }
    } catch(e) { document.getElementById("homepage-error").classList.remove("hidden"); }
    btn.disabled = false; btn.textContent = "Save Homepage Settings";
}

function updateToggle(cb) {
    var label = cb.closest("label");
    if (!label) return;
    var track = label.querySelector(".hp-track");
    var thumb = label.querySelector(".hp-thumb");
    if (track) track.style.background = cb.checked ? "#ec5b13" : "#e2e8f0";
    if (thumb) thumb.style.transform  = cb.checked ? "translateX(20px)" : "translateX(0)";
}
document.addEventListener("change", function(e) {
    if (e.target.classList.contains("hp-toggle")) updateToggle(e.target);
});

async function loadMessages() {
    var list = document.getElementById("messages-list");
    var data = await api("../php/api/contact_messages.php");
    if (!data || !data.length) {
        list.innerHTML = "<div class=\"text-center py-12 text-slate-400\">No messages yet</div>";
        document.getElementById("msg-count").textContent = "0 messages";
        return;
    }
    document.getElementById("msg-count").textContent = data.length + " message" + (data.length !== 1 ? "s" : "");
    list.innerHTML = data.map(function(m) {
        var d = new Date(m.created_at).toLocaleDateString("en-IN", {day:"numeric",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit"});
        return "<div class=\"p-5 hover:bg-slate-50 transition-colors\">" +
            "<div class=\"flex items-start justify-between gap-4 mb-2\">" +
                "<div>" +
                    "<p class=\"font-semibold text-sm text-slate-900\">" + escHtml(m.first_name + " " + (m.last_name||"")) + "</p>" +
                    "<p class=\"text-xs text-slate-400\">" + escHtml(m.email) + " · " + d + "</p>" +
                "</div>" +
                "<span class=\"text-xs bg-primary/10 text-primary px-2 py-0.5 rounded-full font-semibold shrink-0\">" + escHtml(m.interest||"General") + "</span>" +
            "</div>" +
            "<p class=\"text-sm text-slate-600 leading-relaxed\">" + escHtml(m.message) + "</p>" +
        "</div>";
    }).join("");
}

function escHtml(s) {
    return String(s||"").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");
}

switchSection("about");
loadAbout();
loadContact();
loadHomepage();
</script>';
require 'admin-footer.php';
?>
