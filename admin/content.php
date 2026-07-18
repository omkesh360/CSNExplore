<?php
$admin_page  = 'content';
$admin_title = 'Homepage Manager | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6 pb-24">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Homepage Manager</h2>
            <p class="text-xs text-slate-500 font-medium">Select which listings and blogs appear on each homepage section. Check items to pin them; leave all unchecked to auto-show latest.</p>
        </div>
        <button onclick="saveHomepage()" id="hp-save-btn-top"
                class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all shadow-sm">
            💾 Save Changes
        </button>
    </div>

    <div id="homepage-success" class="hidden text-sm text-green-700 bg-green-50 border border-green-200 px-4 py-3 rounded-xl font-medium">✓ Homepage selections saved successfully!</div>
    <div id="homepage-error"   class="hidden text-sm text-red-700   bg-red-50   border border-red-200   px-4 py-3 rounded-xl font-medium">✗ Save failed. Please try again.</div>

    <!-- Sections -->
    <div class="space-y-4">
        <?php
        $hp_sections = [
            'stays'       => ['icon' => '🏨', 'label' => 'Premium Stays',      'desc' => 'Hotels & resorts'],
            'cars'        => ['icon' => '🚗', 'label' => 'Self Drive Cars',    'desc' => 'Car rentals & self-drive'],
            'bikes'       => ['icon' => '🏍️', 'label' => 'Quick Bike Rentals', 'desc' => 'Bikes & scooters for rent'],
            'attractions' => ['icon' => '🏛️', 'label' => 'Ancient Marvels',    'desc' => 'Heritage sites & attractions'],
            'restaurants' => ['icon' => '🍽️', 'label' => 'Taste the City',     'desc' => 'Restaurants & dining'],
            'buses'       => ['icon' => '🚌', 'label' => 'Travel Your Way',    'desc' => 'Bus routes & operators'],
            'blogs'       => ['icon' => '📝', 'label' => 'Travel Insights',    'desc' => 'Blog posts & travel guides'],
        ];
        ?>

        <?php foreach ($hp_sections as $key => $sec): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" id="card-<?php echo $key; ?>">
            <!-- Section Header -->
            <button type="button"
                    class="w-full flex items-center justify-between px-5 py-4 hover:bg-slate-50 transition-colors"
                    onclick="toggleSection('<?php echo $key; ?>')">
                <div class="flex items-center gap-3">
                    <span class="w-11 h-11 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-2xl"><?php echo $sec['icon']; ?></span>
                    <div class="text-left">
                        <span class="font-bold text-slate-800 text-sm block"><?php echo htmlspecialchars($sec['label']); ?></span>
                        <span class="text-xs text-slate-400"><?php echo htmlspecialchars($sec['desc']); ?></span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs bg-orange-50 text-primary font-bold px-2.5 py-1 rounded-full border border-orange-100" id="badge-<?php echo $key; ?>">loading…</span>
                    <span class="text-slate-400 text-sm font-bold transition-transform duration-200" id="chevron-<?php echo $key; ?>">▼</span>
                </div>
            </button>

            <!-- Items Grid -->
            <div id="sec-<?php echo $key; ?>" class="border-t border-slate-100 bg-slate-50/40">
                <div class="flex items-center justify-between px-5 pt-3 pb-2">
                    <p class="text-xs text-slate-400">✓ Checked = pinned on homepage &nbsp;·&nbsp; Unchecked = auto-latest</p>
                    <button type="button" onclick="clearSection('<?php echo $key; ?>')"
                            class="text-[11px] text-slate-400 hover:text-red-500 font-semibold transition-colors">
                        Clear all
                    </button>
                </div>
                <div id="hp-pick-list-<?php echo $key; ?>"
                     class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 px-5 pb-5 max-h-80 overflow-y-auto">
                    <div class="col-span-full flex items-center justify-center py-8 gap-2 text-slate-300">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        <span class="text-xs">Loading items…</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="flex justify-end pt-2">
        <button onclick="saveHomepage()" id="hp-save-btn"
                class="bg-primary text-white px-8 py-3 rounded-xl text-base font-bold hover:bg-orange-600 transition-all shadow-md">
            💾 Save Homepage Selections
        </button>
    </div>
</div>

<?php
$extra_js = <<<'EOT'
<script>
/* ── helpers ── */
function escHtml(s) {
    return String(s||"").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");
}

function toggleSection(key) {
    var body    = document.getElementById("sec-" + key);
    var chevron = document.getElementById("chevron-" + key);
    var hidden  = body.style.display === "none";
    body.style.display   = hidden ? "" : "none";
    if (chevron) chevron.textContent = hidden ? "▲" : "▼";
}

function clearSection(key) {
    document.querySelectorAll("#hp-pick-list-" + key + " input[type=checkbox]").forEach(function(cb){
        cb.checked = false;
        cb.closest("label").classList.remove("ring-2","ring-orange-400","bg-orange-50");
    });
    updateBadge(key, 0, document.querySelectorAll("#hp-pick-list-" + key + " input[type=checkbox]").length);
}

function updateBadge(key, checked, total) {
    if (checked === undefined) {
        checked = document.querySelectorAll("#hp-pick-list-" + key + " input[type=checkbox]:checked").length;
        total   = document.querySelectorAll("#hp-pick-list-" + key + " input[type=checkbox]").length;
    }
    var el = document.getElementById("badge-" + key);
    if (el) {
        if (checked > 0) {
            el.textContent = checked + " / " + total + " pinned";
            el.style.background = "#fff7ed";
            el.style.color = "#ea580c";
        } else {
            el.textContent = total + " available";
            el.style.background = "";
            el.style.color = "";
        }
    }
}

/* ── render items grid ── */
async function loadPickItems(key, savedPicks) {
    savedPicks = (savedPicks || []).map(Number);
    var container = document.getElementById("hp-pick-list-" + key);

    try {
        var res   = await fetch("../php/api/hp_items.php?table=" + key);
        var items = await res.json();

        if (!items || !items.length) {
            container.innerHTML = "<div class=\"col-span-full text-center py-8 text-slate-400 text-xs italic\">No active items found in database.</div>";
            document.getElementById("badge-" + key).textContent = "0 items";
            return;
        }

        container.innerHTML = items.map(function(item) {
            var isPinned = savedPicks.indexOf(Number(item.id)) !== -1;
            var ringCls  = isPinned ? " ring-2 ring-orange-400 bg-orange-50" : "";
            var checkAttr = isPinned ? " checked" : "";

            var imgHtml = item.image
                ? "<img src=\"" + escHtml(item.image) + "\" alt=\"\" class=\"w-full h-20 object-cover\" loading=\"lazy\" onerror=\"this.parentElement.innerHTML=\\'<div class=\\\\\\'w-full h-20 flex items-center justify-center bg-slate-100 text-2xl\\\\\\'>' + (item.icon||\\\'📷\\\') + \\\'</div>\\'\" />"
                : "<div class=\"w-full h-20 flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-2xl\">📷</div>";

            return "<label class=\"relative flex flex-col rounded-xl border border-slate-200 overflow-hidden cursor-pointer hover:border-orange-300 hover:shadow-md transition-all select-none" + ringCls + "\" onclick=\"togglePick(this,\'" + key + "\')\">" +
                "<input type=\"checkbox\" value=\"" + item.id + "\"" + checkAttr + " class=\"hidden\" />" +
                imgHtml +
                "<div class=\"px-2 py-1.5\">" +
                    "<p class=\"text-xs font-semibold text-slate-700 leading-tight line-clamp-2\">" + escHtml(item.name) + "</p>" +
                    (item.type ? "<p class=\"text-[10px] text-slate-400 mt-0.5\">" + escHtml(item.type) + "</p>" : "") +
                "</div>" +
                "<div class=\"absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center transition-all check-indicator\">" +
                    "<svg class=\"w-3 h-3 text-orange-500 hidden check-tick\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\" stroke-width=\"3\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M5 13l4 4L19 7\"/></svg>" +
                "</div>" +
            "</label>";
        }).join("");

        // Apply initial checked states visually
        container.querySelectorAll("label").forEach(function(lbl) {
            var cb = lbl.querySelector("input[type=checkbox]");
            if (cb && cb.checked) {
                lbl.classList.add("ring-2","ring-orange-400","bg-orange-50");
                var tick = lbl.querySelector(".check-tick");
                var indicator = lbl.querySelector(".check-indicator");
                if (tick) tick.classList.remove("hidden");
                if (indicator) indicator.classList.add("border-orange-400");
            }
        });

        updateBadge(key);
    } catch(e) {
        container.innerHTML = "<div class=\"col-span-full text-center py-6 text-red-400 text-xs\">Failed to load items. Check console.</div>";
        console.error("loadPickItems error:", e);
    }
}

function togglePick(label, key) {
    var cb = label.querySelector("input[type=checkbox]");
    if (!cb) return;
    cb.checked = !cb.checked;
    var tick      = label.querySelector(".check-tick");
    var indicator = label.querySelector(".check-indicator");
    if (cb.checked) {
        label.classList.add("ring-2","ring-orange-400","bg-orange-50");
        if (tick) tick.classList.remove("hidden");
        if (indicator) indicator.classList.add("border-orange-400");
    } else {
        label.classList.remove("ring-2","ring-orange-400","bg-orange-50");
        if (tick) tick.classList.add("hidden");
        if (indicator) indicator.classList.remove("border-orange-400");
    }
    updateBadge(key);
}

/* ── load saved picks ── */
async function loadHomepage() {
    var data = await api("../php/api/about_contact.php?section=homepage");
    if (!data) {
        // Still load all items even if no saved picks
        ["stays","cars","bikes","attractions","restaurants","buses","blogs"].forEach(function(k){
            loadPickItems(k, []);
        });
        return;
    }
    ["stays","cars","bikes","attractions","restaurants","buses","blogs"].forEach(function(k) {
        loadPickItems(k, data["picks_" + k] || []);
    });
}

/* ── save ── */
async function saveHomepage() {
    var btn    = document.getElementById("hp-save-btn");
    var btnTop = document.getElementById("hp-save-btn-top");
    btn.disabled = true;    btn.textContent    = "Saving…";
    btnTop.disabled = true; btnTop.textContent = "Saving…";

    // Load existing data first to preserve hero, testimonials etc.
    var existing = await api("../php/api/about_contact.php?section=homepage") || {};

    ["stays","cars","bikes","attractions","restaurants","buses","blogs"].forEach(function(k) {
        existing["picks_" + k] = Array.from(
            document.querySelectorAll("#hp-pick-list-" + k + " input[type=checkbox]:checked")
        ).map(function(el){ return parseInt(el.value, 10); });
    });

    try {
        var res = await api("../php/api/about_contact.php", {
            method: "PUT",
            body: JSON.stringify({ section: "homepage", data: existing })
        });
        var ok = document.getElementById("homepage-success");
        var er = document.getElementById("homepage-error");
        if (res && res.success) {
            ok.classList.remove("hidden"); er.classList.add("hidden");
            window.scrollTo({top: 0, behavior: "smooth"});
            setTimeout(function(){ ok.classList.add("hidden"); }, 3000);
        } else {
            er.classList.remove("hidden"); ok.classList.add("hidden");
            setTimeout(function(){ er.classList.add("hidden"); }, 4000);
        }
    } catch(e) {
        document.getElementById("homepage-error").classList.remove("hidden");
        console.error(e);
    }
    btn.disabled = false;    btn.textContent    = "💾 Save Homepage Selections";
    btnTop.disabled = false; btnTop.textContent = "💾 Save Changes";
}

loadHomepage();
</script>
EOT;

require 'admin-footer.php';
?>
