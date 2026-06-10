<?php
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/jwt.php';

$admin_token_cookie = $_COOKIE['admin_token'] ?? null;
if (!$admin_token_cookie) {
    header('Location: ../adminexplorer.php?session_expired=1');
    exit;
}

$payload = verifyJWT($admin_token_cookie, JWT_SECRET);
if (!$payload || !isset($payload['role']) || $payload['role'] !== 'admin') {
    header('Location: ../adminexplorer.php?session_expired=1');
    exit;
}

$admin_page  = 'blogs';
$admin_title = 'Edit Blog | CSNExplore Admin';
$blog_id = $_GET['id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $blog_id ? 'Edit' : 'New'; ?> Post | CSNExplore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#ec5b13' }, fontFamily: { sans: ['Inter','sans-serif'] } } }
        }
    </script>
    <style>
        body { background: #f8fafc; font-family: 'Inter', sans-serif; }
        .ql-toolbar.ql-snow { border: none; border-bottom: 1px solid #e2e8f0; background: #fff; position: sticky; top: 0; z-index: 10; padding: 12px 24px; }
        .ql-container.ql-snow { border: none !important; font-family: 'Inter', sans-serif; font-size: 16px; min-height: calc(100vh - 400px); }
        .ql-editor { padding: 40px 24px; max-width: 800px; margin: 0 auto; line-height: 1.8; color: #1e293b; }
        .ql-editor p { margin-bottom: 1.5rem; }
        .ql-editor h1, .ql-editor h2, .ql-editor h3 { font-weight: 800; color: #0f172a; margin-top: 2rem; margin-bottom: 1rem; }
        .ql-editor.ql-blank::before { left: 24px; max-width: 800px; margin: 0 auto; font-style: normal; color: #94a3b8; font-size: 1.25rem; }
        
        /* WordPress Style Sidebar */
        .wp-sidebar { width: 360px; border-left: 1px solid #e2e8f0; background: #fff; height: 100%; overflow-y: auto; }
        .wp-header { height: 60px; border-bottom: 1px solid #e2e8f0; background: #fff; display: flex; items-center: center; justify-content: space-between; padding: 0 24px; position: sticky; top: 0; z-index: 20; }
        
        input:focus, select:focus, textarea:focus { outline: none; border-color: #ec5b13; ring: 0; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .seo-panel { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
    </style>
    <!-- CSRF Protection Fetch Interceptor -->
    <script>
    (function() {
        const originalFetch = window.fetch;
        window.fetch = function(url, options) {
            options = options || {};
            const method = (options.method || 'GET').toUpperCase();
            if (['POST', 'PUT', 'DELETE'].includes(method)) {
                const isRelative = !url.match(/^(?:https?:)?\/\//i);
                const isSameOrigin = url.startsWith(window.location.origin);
                if (isRelative || isSameOrigin) {
                    options.headers = options.headers || {};
                    const matches = document.cookie.match(/(?:^|; )csrf_token=([^;]*)/);
                    const csrfToken = matches ? decodeURIComponent(matches[1]) : '';
                    if (csrfToken) {
                        if (options.headers instanceof Headers) {
                            options.headers.set('X-CSRF-Token', csrfToken);
                        } else if (Array.isArray(options.headers)) {
                            options.headers.push(['X-CSRF-Token', csrfToken]);
                        } else {
                            options.headers['X-CSRF-Token'] = csrfToken;
                        }
                    }
                }
            }
            return originalFetch(url, options);
        };
    })();
    </script>
</head>
<body class="overflow-x-hidden">

<div class="flex flex-col h-screen overflow-hidden">
    <!-- Top Nav -->
    <header class="wp-header shrink-0">
        <div class="flex items-center gap-4">
            <a href="blogs.php" class="p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-primary rounded flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-lg">edit_note</span>
                </div>
                <span class="font-bold text-slate-900">Blog Editor</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <span id="save-status" class="text-xs text-slate-400 font-medium mr-2">Draft</span>
            <button onclick="savePost('draft')" class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">Save Draft</button>
            <button onclick="savePost('published')" id="publish-btn" class="px-5 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-orange-600 transition-all shadow-sm">Publish</button>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden min-h-0">
        <!-- Editor Area -->
        <main class="flex-1 overflow-y-auto bg-white custom-scrollbar min-h-0">
            <div class="max-w-[800px] mx-auto px-6 py-12">
                <input type="text" id="post-title" placeholder="Add title" 
                       class="w-full text-4xl md:text-5xl font-black text-slate-900 border-none px-0 mb-8 placeholder-slate-200 focus:placeholder-slate-100 bg-transparent"
                       oninput="calculateSEOScore()">
                
                <div id="editor-container"></div>
            </div>
        </main>

        <!-- Sidebar -->
        <aside class="wp-sidebar custom-scrollbar p-6 space-y-6 min-h-0 shrink-0 pb-20">
            <!-- Status & Visibility -->
            <section>
                <div class="flex items-center gap-2 mb-4 text-slate-900">
                    <span class="material-symbols-outlined text-xl">settings</span>
                    <h3 class="font-bold text-sm">Post Settings</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Author</label>
                        <input type="text" id="post-author" value="Admin" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Category</label>
                        <input type="text" id="post-category" value="General" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Read Time</label>
                        <input type="text" id="post-read-time" placeholder="e.g. 5 min read" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </section>

            <!-- Featured Image -->
            <section class="pt-6 border-t border-slate-100">
                <div class="flex items-center gap-2 mb-4 text-slate-900">
                    <span class="material-symbols-outlined text-xl">image</span>
                    <h3 class="font-bold text-sm">Featured Image</h3>
                </div>
                <div id="image-preview-container" class="aspect-video bg-slate-50 border border-dashed border-slate-200 rounded-xl overflow-hidden mb-3 group relative cursor-pointer" onclick="document.getElementById('post-image').focus()">
                    <img loading="lazy" width="800" height="600" id="image-preview" src="" class="hidden w-full h-full object-cover">
                    <div id="image-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 group-hover:text-slate-500">
                        <span class="material-symbols-outlined text-3xl mb-1">add_photo_alternate</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider">Set Featured Image</span>
                    </div>
                </div>
                <div class="flex gap-2 mb-2">
                    <label for="post-image-file" onclick="window.imageTarget = 'featured';"
                           class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-primary text-white text-xs font-bold rounded-lg cursor-pointer hover:bg-orange-600 transition-all">
                        <span class="material-symbols-outlined text-sm">upload</span>
                        Upload Image
                    </label>
                    <input type="file" id="post-image-file" accept="image/*" class="hidden" onchange="uploadBlogImage(this)">
                    <button type="button" onclick="window.imageTarget = 'featured'; openGalleryModal();"
                            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined text-sm">photo_library</span>
                        From Gallery
                    </button>
                </div>
                <input type="url" id="post-image" placeholder="Or paste image URL" oninput="previewImage(this.value)" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <div id="upload-progress" class="hidden mt-2 text-xs text-slate-500 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Uploading...
                </div>
            </section>

            <!-- SEO Section - WordPress Yoast Style -->
            <section class="pt-6 border-t border-slate-100">
                <div class="flex items-center gap-2 mb-4 text-slate-900">
                    <span class="material-symbols-outlined text-xl text-primary">search</span>
                    <h3 class="font-bold text-sm">SEO Optimization</h3>
                </div>
                
                <div class="seo-panel p-4 rounded-xl border border-slate-200 space-y-4">
                    <!-- SEO Score -->
                    <div class="bg-white border border-slate-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-600">SEO Score</span>
                            <span id="seo-score-display" class="text-2xl font-black text-slate-300">--</span>
                        </div>
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div id="seo-score-bar" class="h-full bg-slate-300 transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <div id="seo-tips" class="mt-3 space-y-1.5 text-[10px] text-slate-500"></div>
                    </div>
                    
                    <!-- Focus Keyword -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Focus Keyword</label>
                        <input id="post-focus-keyword" type="text" placeholder="e.g. travel guide sambhajinagar" 
                               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"
                               oninput="calculateSEOScore()">
                        <p class="text-[9px] text-slate-400 mt-1">Main keyword to rank for</p>
                    </div>
                    
                    <!-- SEO Title -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>SEO Title</span>
                            <span id="meta-title-count" class="text-[10px] text-slate-400 font-normal">0/60</span>
                        </label>
                        <input id="post-meta-title" type="text" maxlength="60" placeholder="Optimized title for search engines" 
                               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"
                               oninput="updateCharCount('meta-title-count', this.value, 60); calculateSEOScore()">
                        <p class="text-[9px] text-slate-400 mt-1">Recommended: 50-60 characters</p>
                    </div>
                    
                    <!-- Meta Description -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Meta Description</span>
                            <span id="meta-desc-count" class="text-[10px] text-slate-400 font-normal">0/160</span>
                        </label>
                        <textarea id="post-meta-desc" rows="3" maxlength="160" placeholder="Brief description for search results" 
                                  class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm resize-none"
                                  oninput="updateCharCount('meta-desc-count', this.value, 160); calculateSEOScore()"></textarea>
                        <p class="text-[9px] text-slate-400 mt-1">Recommended: 150-160 characters</p>
                    </div>
                    
                    <!-- URL Slug -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">URL Slug</label>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-slate-400">/blog-detail/</span>
                            <input id="post-slug" type="text" placeholder="auto-generated" 
                                   class="flex-1 border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-mono"
                                   oninput="calculateSEOScore()">
                        </div>
                        <p class="text-[9px] text-slate-400 mt-1">Leave empty to auto-generate</p>
                    </div>
                    
                    <!-- Meta Keywords -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Meta Keywords</label>
                        <input id="post-meta-keywords" type="text" placeholder="keyword1, keyword2, keyword3" 
                               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"
                               oninput="calculateSEOScore()">
                        <p class="text-[9px] text-slate-400 mt-1">Comma-separated keywords</p>
                    </div>
                </div>
            </section>

            <!-- Excerpt & Tags -->
            <section class="pt-6 border-t border-slate-100">
                <div class="flex items-center gap-2 mb-4 text-slate-900">
                    <span class="material-symbols-outlined text-xl">label</span>
                    <h3 class="font-bold text-sm">Summary & Tags</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Excerpt</label>
                        <textarea id="post-excerpt" rows="3" placeholder="Brief summary for cards..." class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tags</label>
                        <input type="text" id="post-tags" placeholder="comma-separated" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </section>

            <!-- Linked Listings -->
            <section class="pt-6 border-t border-slate-100">
                <div class="flex items-center gap-2 mb-4 text-slate-900">
                    <span class="material-symbols-outlined text-xl text-primary">storefront</span>
                    <h3 class="font-bold text-sm">Linked Listings</h3>
                </div>
                <p class="text-[10px] text-slate-400 mb-3">Attach relevant listings (hotels, cars, bikes, etc.) to showcase inside this blog post.</p>
                
                <div class="space-y-3">
                    <div class="flex gap-2">
                        <select id="listing-type-pick" class="flex-1 border border-slate-200 rounded-lg px-2 py-2 text-xs" onchange="fetchCategoryListings()">
                            <option value="stays">🏨 Hotel/Stay</option>
                            <option value="cars">🚗 Car Rental</option>
                            <option value="bikes">🏍️ Bike Rental</option>
                            <option value="attractions">🎟️ Attraction</option>
                            <option value="restaurants">🍽️ Restaurant</option>
                            <option value="buses">🚌 Bus Route</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <select id="listing-id-input" class="flex-1 border border-slate-200 rounded-lg px-2 py-2 text-xs">
                            <option value="">Loading...</option>
                        </select>
                        <button onclick="addLinkedListing()" 
                                class="px-3 py-2 bg-primary text-white text-xs font-bold rounded-lg hover:bg-orange-600 transition-all flex items-center gap-1 shrink-0">
                            <span class="material-symbols-outlined text-sm">add</span>Add
                        </button>
                    </div>
                    <div id="linked-listings-preview" class="space-y-2 max-h-56 overflow-y-auto"></div>
                    <p class="text-[9px] text-slate-400">Select a listing to showcase inside this blog post.</p>
                </div>
            </section>

            <div id="error-box" class="hidden p-4 bg-red-50 border border-red-100 text-red-600 text-xs rounded-xl"></div>
        </aside>
    </div>
</div>

<script>
    var quill;
    var blogId = '<?php echo $blog_id; ?>';
    var isSaving = false;

    // Load API helper
    async function api(url, options = {}) {
        const token = localStorage.getItem('csn_admin_token');
        options.headers = options.headers || {};
        options.headers['Content-Type'] = 'application/json';
        if (token) options.headers['Authorization'] = 'Bearer ' + token;
        
        const res = await fetch(url, options);
        if (res.status === 401) { window.location.href = '../adminexplorer.php'; return null; }
        return res.json();
    }

    document.addEventListener('DOMContentLoaded', function() {
        quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Start writing your story...',
            modules: {
                toolbar: {
                    container: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link', 'image', 'blockquote', 'code-block'],
                        [{ align: [] }],
                        ['clean']
                    ],
                    handlers: {
                        image: function() {
                            window.imageTarget = 'editor';
                            openGalleryModal();
                        }
                    }
                }
            }
        });

        quill.on('text-change', calculateSEOScore);

        if (blogId) {
            loadPostData();
        }
        
        fetchCategoryListings();

        // ── Intercept Image Pasting ──────────────────────────────────────────
        document.getElementById('editor-container').addEventListener('paste', function(e) {
            if (e.clipboardData && e.clipboardData.items) {
                var items = e.clipboardData.items;
                var hasImage = false;
                for (var i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image') !== -1) {
                        var file = items[i].getAsFile();
                        if (file) {
                            e.preventDefault();
                            hasImage = true;
                            uploadPastedImage(file);
                        }
                    }
                }
                // If it was just an image, we prevented default. Text pastes bypass this.
            }
        });
    });

    async function uploadPastedImage(file) {
        const errBox = document.getElementById('error-box');
        const formData = new FormData();
        // The API expects 'image' or 'file', our upload.php handles both.
        // We will pass 'file' as the name. But let's check upload.php. It accepts both.
        formData.append('image', file, 'pasted-image.png');
        
        try {
            const token = localStorage.getItem('csn_admin_token');
            // Show a tiny toast or indicator (optional)
            const res = await fetch('../php/api/upload.php', {
                method: 'POST',
                headers: token ? {'Authorization': 'Bearer ' + token} : {},
                body: formData
            });
            const data = await res.json();
            if (data.url) {
                insertImageToEditor(data.url);
            } else {
                showErr(data.error || 'Upload failed');
            }
        } catch(e) {
            showErr('Paste Upload error: ' + e.message);
        }
    }

    async function loadPostData() {
        try {
            const data = await api('../php/api/blogs.php?id=' + blogId);
            if (!data) { showErr('API returned empty response'); return; }
            if (data.error) { showErr('Failed to load blog: ' + data.error); return; }
            
            document.getElementById('post-title').value = data.title || '';
        document.getElementById('post-author').value = data.author || 'Admin';
        document.getElementById('post-category').value = data.category || 'General';
        document.getElementById('post-read-time').value = data.read_time || '';
        document.getElementById('post-image').value = data.image || '';
        document.getElementById('post-tags').value = (data.tags || []).join(', ');
        document.getElementById('post-excerpt').value = data.excerpt || data.meta_description || '';
        document.getElementById('post-meta-title').value = data.meta_title || '';
        document.getElementById('post-meta-desc').value = data.meta_description || '';
        document.getElementById('post-meta-keywords').value = data.meta_keywords || '';
        document.getElementById('post-focus-keyword').value = data.focus_keyword || '';
        document.getElementById('post-slug').value = data.slug || '';
        document.getElementById('save-status').textContent = (data.status || 'draft').toUpperCase();
        
        if (data.image) previewImage(data.image);
        if (data.content) quill.clipboard.dangerouslyPasteHTML(data.content);
        
        // Load linked listings
        if (data.linked_listings && Array.isArray(data.linked_listings)) {
            data.linked_listings.forEach(ll => addLinkedListingItem(ll.type, ll.id, ll.name || ''));
        }
        
        updateCharCount('meta-title-count', data.meta_title || '', 60);
        updateCharCount('meta-desc-count', data.meta_description || '', 160);
        calculateSEOScore();
        } catch (e) {
            showErr('Exception loading post: ' + e.message);
        }
    }

    function previewImage(url) {
        const img = document.getElementById('image-preview');
        const pl = document.getElementById('image-placeholder');
        if (url) {
            let resolvedUrl = url;
            if (!url.startsWith('http') && !url.startsWith('/')) {
                resolvedUrl = '../' + url;
            }
            img.src = resolvedUrl;
            img.classList.remove('hidden');
            pl.classList.add('hidden');
        } else {
            img.classList.add('hidden');
            pl.classList.remove('hidden');
        }
    }

    function updateCharCount(elemId, value, max) {
        var count = (value || '').length;
        var elem = document.getElementById(elemId);
        if (elem) {
            elem.textContent = count + '/' + max;
            elem.classList.toggle('text-red-500', count > max);
            elem.classList.toggle('text-green-500', count >= max * 0.8 && count <= max);
        }
    }

    function calculateSEOScore() {
        var score = 0;
        var tips = [];
        
        var titleInput = document.getElementById('post-title');
        var title = titleInput ? titleInput.value : '';
        
        var metaTitleInput = document.getElementById('post-meta-title');
        var metaTitle = metaTitleInput ? metaTitleInput.value : '';
        
        var metaDescInput = document.getElementById('post-meta-desc');
        var metaDesc = metaDescInput ? metaDescInput.value : '';
        
        var focusInput = document.getElementById('post-focus-keyword');
        var focus = focusInput ? focusInput.value : '';
        
        var slugInput = document.getElementById('post-slug');
        var slug = slugInput ? slugInput.value : '';
        
        var keywordsInput = document.getElementById('post-meta-keywords');
        var keywords = keywordsInput ? keywordsInput.value : '';
        
        var content = typeof quill !== 'undefined' && quill ? quill.getText() : '';
        
        // Title checks (25 points)
        if (metaTitle.length >= 50 && metaTitle.length <= 60) {
            score += 15;
        } else if (metaTitle.length > 0) {
            score += 5;
            tips.push('SEO title should be 50-60 characters');
        } else {
            tips.push('Add an SEO title');
        }
        
        if (focus && metaTitle.toLowerCase().includes(focus.toLowerCase())) {
            score += 10;
        } else if (focus && metaTitle) {
            tips.push('Include focus keyword in SEO title');
        }
        
        // Description checks (25 points)
        if (metaDesc.length >= 150 && metaDesc.length <= 160) {
            score += 15;
        } else if (metaDesc.length > 0) {
            score += 5;
            tips.push('Meta description should be 150-160 characters');
        } else {
            tips.push('Add a meta description');
        }
        
        if (focus && metaDesc.toLowerCase().includes(focus.toLowerCase())) {
            score += 10;
        } else if (focus && metaDesc) {
            tips.push('Include focus keyword in meta description');
        }
        
        // Focus keyword in content (20 points)
        if (focus && content.toLowerCase().includes(focus.toLowerCase())) {
            var escapedFocus = focus.toLowerCase().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var density = (content.toLowerCase().match(new RegExp(escapedFocus, 'g')) || []).length;
            if (density >= 3 && density <= 10) {
                score += 20;
            } else if (density > 0) {
                score += 10;
                tips.push('Focus keyword density could be better (aim for 3-10 occurrences)');
            }
        } else if (focus) {
            tips.push('Use focus keyword in content');
        } else {
            tips.push('Set a focus keyword');
        }
        
        // Content length (15 points)
        var wordCount = content.trim().split(/\s+/).length;
        if (wordCount >= 300) {
            score += 15;
        } else if (wordCount >= 150) {
            score += 8;
            tips.push('Content is a bit short (aim for 300+ words)');
        } else if (wordCount > 0) {
            score += 3;
            tips.push('Content is too short (aim for 300+ words)');
        } else {
            tips.push('Add content to your post');
        }
        
        // Slug (10 points)
        if (slug && slug.length > 0) {
            score += 10;
        } else {
            tips.push('Set a URL slug');
        }
        
        // Meta keywords (5 points)
        if (keywords && keywords.split(',').filter(k => k.trim()).length >= 3) {
            score += 5;
        } else {
            tips.push('Add at least 3 meta keywords');
        }
        
        // Update UI
        var scoreDisplay = document.getElementById('seo-score-display');
        var scoreBar = document.getElementById('seo-score-bar');
        var tipsContainer = document.getElementById('seo-tips');
        
        if (scoreDisplay) {
            scoreDisplay.textContent = score;
            scoreDisplay.className = 'text-2xl font-black ' + 
                (score >= 80 ? 'text-green-500' : score >= 50 ? 'text-amber-500' : 'text-red-500');
        }
        
        if (scoreBar) {
            scoreBar.style.width = score + '%';
            scoreBar.className = 'h-full transition-all duration-300 ' + 
                (score >= 80 ? 'bg-green-500' : score >= 50 ? 'bg-amber-500' : 'bg-red-500');
        }
        
        if (tipsContainer) {
            tipsContainer.innerHTML = tips.length > 0 
                ? tips.map(t => '<div class="flex items-start gap-1.5"><span class="material-symbols-outlined text-[14px] text-amber-500 shrink-0">info</span><span>' + t + '</span></div>').join('')
                : '<div class="text-green-600 flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">check_circle</span><span>SEO looks great!</span></div>';
        }
        
        return score;
    }

    async function savePost(status) {
        if (isSaving) return;
        
        const title = document.getElementById('post-title').value;
        const content = quill.root.innerHTML;
        const errBox = document.getElementById('error-box');
        
        if (!title) { showErr('Title is required'); return; }
        if (content === '<p><br></p>') { showErr('Content cannot be empty'); return; }
        
        isSaving = true;
        errBox.classList.add('hidden');
        const publishBtn = document.getElementById('publish-btn');
        const oldBtnText = publishBtn.textContent;
        publishBtn.textContent = 'Saving...';
        
        const tags = document.getElementById('post-tags').value.split(',').map(s => s.trim()).filter(Boolean);
        const seoScore = calculateSEOScore();
        const linkedListings = getLinkedListings();
        
        const data = {
            title,
            author: document.getElementById('post-author').value,
            category: document.getElementById('post-category').value,
            read_time: document.getElementById('post-read-time').value,
            status: status || 'draft',
            image: document.getElementById('post-image').value,
            tags,
            excerpt: document.getElementById('post-excerpt').value,
            meta_title: document.getElementById('post-meta-title').value,
            meta_description: document.getElementById('post-meta-desc').value,
            meta_keywords: document.getElementById('post-meta-keywords').value,
            focus_keyword: document.getElementById('post-focus-keyword').value,
            // Auto-generate slug from title if empty
            slug: document.getElementById('post-slug').value || title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').substring(0, 80),
            seo_score: seoScore,
            linked_listings: linkedListings,
            content
        };

        try {
            let url = '../php/api/blogs.php';
            let method = 'POST';
            if (blogId) { url += '?id=' + blogId; method = 'PUT'; }
            
            const res = await api(url, { method, body: JSON.stringify(data) });
            if (res && res.error) throw new Error(res.error);
            
            if (!blogId && res.id) {
                blogId = res.id;
                window.history.replaceState({}, '', 'blog-editor-new.php?id=' + blogId);
            }
            
            document.getElementById('save-status').textContent = data.status.toUpperCase();
            document.getElementById('save-status').classList.add('text-green-500');
            setTimeout(() => document.getElementById('save-status').classList.remove('text-green-500'), 2000);
            
        } catch (e) {
            showErr(e.message);
        } finally {
            isSaving = false;
            publishBtn.textContent = oldBtnText;
        }
    }

    function showErr(msg) {
        const errBox = document.getElementById('error-box');
        errBox.textContent = msg;
        errBox.classList.remove('hidden');
        setTimeout(() => errBox.classList.add('hidden'), 5000);
    }

    async function uploadBlogImage(input) {
        if (!input.files || !input.files[0]) return;
        const progress = document.getElementById('upload-progress');
        progress.classList.remove('hidden');
        const formData = new FormData();
        formData.append('image', input.files[0]);
        try {
            const token = localStorage.getItem('csn_admin_token');
            const res = await fetch('../php/api/upload.php', {
                method: 'POST',
                headers: token ? {'Authorization': 'Bearer ' + token} : {},
                body: formData
            });
            const data = await res.json();
            if (data.url) {
                document.getElementById('post-image').value = data.url;
                previewImage(data.url);
            } else {
                showErr(data.error || 'Upload failed');
            }
        } catch(e) {
            showErr('Upload error: ' + e.message);
        } finally {
            progress.classList.add('hidden');
            input.value = '';
        }
    }

    // ─── Linked Listings ─────────────────────────────────────────────────────
    const _linkedListings = [];

    async function fetchCategoryListings() {
        const type = document.getElementById('listing-type-pick').value;
        const select = document.getElementById('listing-id-input');
        select.innerHTML = '<option value="">Loading...</option>';
        
        try {
            const res = await api(`../php/api/listings.php?category=${type}`);
            if (!res || res.error) throw new Error(res ? res.error : 'Failed to fetch');
            
            select.innerHTML = '<option value="">Select a listing...</option>';
            res.forEach(item => {
                const name = item.name || item.operator || `${type} #${item.id}`;
                let price = '';
                const priceKeys = ['price_per_night', 'price_per_day', 'price_per_person', 'entry_fee', 'price'];
                for (let k of priceKeys) {
                    if (item[k] !== undefined && item[k] !== null && parseFloat(item[k]) > 0) {
                        price = ` - ₹${item[k]}`;
                        break;
                    }
                }
                
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = name + price;
                option.dataset.name = name;
                select.appendChild(option);
            });
        } catch(e) {
            select.innerHTML = '<option value="">Error loading listings</option>';
            showErr(e.message);
        }
    }

    function addLinkedListing() {
        const type = document.getElementById('listing-type-pick').value;
        const select = document.getElementById('listing-id-input');
        const id = parseInt(select.value);
        
        if (!id || isNaN(id)) { showErr('Please select a listing'); return; }
        if (_linkedListings.find(l => l.type === type && l.id === id)) { showErr('Already added'); return; }

        const option = select.options[select.selectedIndex];
        const name = option ? option.dataset.name : `${type} #${id}`;
        
        addLinkedListingItem(type, id, name);
        select.value = '';
    }

    function addLinkedListingItem(type, id, name) {
        if (_linkedListings.find(l => l.type === type && l.id === id)) return;
        _linkedListings.push({ type, id: parseInt(id), name });

        const icons = { stays:'🏨', cars:'🚗', bikes:'🏍️', attractions:'🎟️', restaurants:'🍽️', buses:'🚌' };
        const div = document.createElement('div');
        div.id = `ll-${type}-${id}`;
        div.className = 'flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs';
        div.innerHTML = `
            <span class="text-base">${icons[type] || '📌'}</span>
            <div class="flex-1 min-w-0">
                <div class="font-semibold truncate">${name}</div>
                <div class="text-slate-400">${type} · ID ${id}</div>
            </div>
            <button onclick="removeLinkedListing('${type}',${id})" class="text-red-400 hover:text-red-600 transition-colors ml-1">
                <span class="material-symbols-outlined text-base">close</span>
            </button>`;
        document.getElementById('linked-listings-preview').appendChild(div);
    }

    function removeLinkedListing(type, id) {
        const idx = _linkedListings.findIndex(l => l.type === type && l.id === id);
        if (idx > -1) _linkedListings.splice(idx, 1);
        const el = document.getElementById(`ll-${type}-${id}`);
        if (el) el.remove();
    }

    function getLinkedListings() {
        return _linkedListings.map(l => ({ type: l.type, id: l.id, name: l.name }));
    }

    // ─── Gallery Modal Functions ─────────────────────────────────────────────
    function openGalleryModal() {
        document.getElementById('gallery-modal').classList.remove('hidden');
        loadGalleryImages();
    }

    function closeGalleryModal() {
        document.getElementById('gallery-modal').classList.add('hidden');
    }

    async function loadGalleryImages() {
        const grid = document.getElementById('gallery-grid');
        const loading = document.getElementById('gallery-loading');
        const empty = document.getElementById('gallery-empty');
        
        grid.innerHTML = '';
        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        
        try {
            const data = await api('../php/api/gallery.php');
            loading.classList.add('hidden');
            
            if (!data || data.length === 0) {
                empty.classList.remove('hidden');
                return;
            }
            
            data.forEach(img => {
                const item = document.createElement('div');
                item.className = 'group relative aspect-square bg-white border border-slate-200 rounded-xl overflow-hidden cursor-pointer hover:border-primary hover:shadow-md transition-all flex flex-col';
                item.onclick = () => selectGalleryImage(img.url);
                
                item.innerHTML = `
                    <div class="flex-1 bg-slate-100 overflow-hidden relative">
                        <img src="${img.url}" alt="${img.filename}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="text-white text-xs font-bold bg-primary/95 px-2.5 py-1 rounded-full shadow-sm">Select</span>
                        </div>
                    </div>
                    <div class="p-1.5 border-t border-slate-100 text-[10px] text-slate-500 font-medium truncate bg-white">
                        ${img.filename}
                    </div>
                `;
                grid.appendChild(item);
            });
        } catch (e) {
            loading.classList.add('hidden');
            showErr('Error loading gallery: ' + e.message);
        }
    }

    function selectGalleryImage(url) {
        if (window.imageTarget === 'editor') {
            insertImageToEditor(url);
        } else {
            document.getElementById('post-image').value = url;
            previewImage(url);
        }
        closeGalleryModal();
    }

    function insertImageToEditor(url) {
        const range = quill.getSelection(true);
        quill.insertEmbed(range.index, 'image', url, Quill.sources.USER);
        quill.setSelection(range.index + 1, Quill.sources.SILENT);
    }

    async function uploadToGallery(input) {
        if (!input.files || !input.files[0]) return;
        const progress = document.getElementById('upload-progress');
        if (progress) progress.classList.remove('hidden');
        
        const formData = new FormData();
        formData.append('image', input.files[0]);
        try {
            const token = localStorage.getItem('csn_admin_token');
            const res = await fetch('../php/api/upload.php', {
                method: 'POST',
                headers: token ? {'Authorization': 'Bearer ' + token} : {},
                body: formData
            });
            const data = await res.json();
            if (data.url) {
                await loadGalleryImages();
                selectGalleryImage(data.url);
            } else {
                showErr(data.error || 'Upload failed');
            }
        } catch(e) {
            showErr('Upload error: ' + e.message);
        } finally {
            if (progress) progress.classList.add('hidden');
            input.value = '';
        }
    }

</script>

<!-- Gallery Modal -->
<div id="gallery-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl flex flex-col max-h-[85vh] overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl">photo_library</span>
                <h3 class="font-bold text-slate-800 text-lg">Image Gallery</h3>
            </div>
            <div class="flex items-center gap-3">
                <label for="gallery-upload-file" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-lg cursor-pointer hover:bg-orange-600 transition-all flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-sm">upload</span>
                    Upload New
                </label>
                <input type="file" id="gallery-upload-file" accept="image/*" class="hidden" onchange="uploadToGallery(this)">
                <button onclick="closeGalleryModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        
        <!-- Gallery Grid Content -->
        <div class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-slate-50">
            <div id="gallery-loading" class="flex flex-col items-center justify-center py-20 text-slate-400">
                <span class="material-symbols-outlined text-4xl animate-spin mb-2">progress_activity</span>
                <span class="text-sm font-medium">Loading gallery images...</span>
            </div>
            <div id="gallery-empty" class="hidden flex flex-col items-center justify-center py-20 text-slate-400">
                <span class="material-symbols-outlined text-4xl mb-2">image_not_supported</span>
                <span class="text-sm font-medium">No images found in gallery</span>
            </div>
            <div id="gallery-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <!-- JavaScript populated -->
            </div>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3 bg-white">
            <button onclick="closeGalleryModal()" class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
        </div>
    </div>
</div>

</body>
</html>
