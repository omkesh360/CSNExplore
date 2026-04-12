<?php
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
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet"/>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
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
        .wp-sidebar { width: 300px; border-left: 1px solid #e2e8f0; background: #fff; height: 100vh; overflow-y: auto; position: sticky; top: 0; }
        .wp-header { height: 60px; border-bottom: 1px solid #e2e8f0; background: #fff; display: flex; items-center: center; justify-content: space-between; padding: 0 24px; position: sticky; top: 0; z-index: 20; }
        
        input:focus, select:focus, textarea:focus { outline: none; border-color: #ec5b13; ring: 0; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="overflow-x-hidden">

<div class="flex flex-col min-h-screen">
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

    <div class="flex flex-1 overflow-hidden">
        <!-- Editor Area -->
        <main class="flex-1 overflow-y-auto bg-white custom-scrollbar">
            <div class="max-w-[800px] mx-auto px-6 py-12">
                <input type="text" id="post-title" placeholder="Add title" 
                       class="w-full text-4xl md:text-5xl font-black text-slate-900 border-none px-0 mb-8 placeholder-slate-200 focus:placeholder-slate-100 bg-transparent">
                
                <div id="editor-container"></div>
            </div>
        </main>

        <!-- Sidebar -->
        <aside class="wp-sidebar custom-scrollbar p-6 space-y-8">
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
                    <img id="image-preview" src="" class="hidden w-full h-full object-cover">
                    <div id="image-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 group-hover:text-slate-500">
                        <span class="material-symbols-outlined text-3xl mb-1">add_photo_alternate</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider">Set Featured Image</span>
                    </div>
                </div>
                <div class="flex gap-2 mb-2">
                    <label for="post-image-file"
                           class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-primary text-white text-xs font-bold rounded-lg cursor-pointer hover:bg-orange-600 transition-all">
                        <span class="material-symbols-outlined text-sm">upload</span>
                        Upload Image
                    </label>
                    <input type="file" id="post-image-file" accept="image/*" class="hidden" onchange="uploadBlogImage(this)">
                </div>
                <input type="url" id="post-image" placeholder="Or paste image URL" oninput="previewImage(this.value)" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <div id="upload-progress" class="hidden mt-2 text-xs text-slate-500 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Uploading...
                </div>
            </section>

            <!-- Metadata -->
            <section class="pt-6 border-t border-slate-100">
                <div class="flex items-center gap-2 mb-4 text-slate-900">
                    <span class="material-symbols-outlined text-xl">label</span>
                    <h3 class="font-bold text-sm">Summary & Tags</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Excerpt</label>
                        <textarea id="post-meta" rows="3" placeholder="Brief summary for cards..." class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tags</label>
                        <input type="text" id="post-tags" placeholder="comma-separated" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    </div>
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
        if (res.status === 401) { window.location.href = '../login.php'; return null; }
        return res.json();
    }

    document.addEventListener('DOMContentLoaded', function() {
        quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Start writing your story...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'blockquote', 'code-block'],
                    [{ align: [] }],
                    ['clean']
                ]
            }
        });

        if (blogId) {
            loadPostData();
        }
    });

    async function loadPostData() {
        const data = await api('../php/api/blogs.php?id=' + blogId);
        if (!data) return;
        
        document.getElementById('post-title').value = data.title || '';
        document.getElementById('post-author').value = data.author || 'Admin';
        document.getElementById('post-category').value = data.category || 'General';
        document.getElementById('post-read-time').value = data.read_time || '';
        document.getElementById('post-image').value = data.image || '';
        document.getElementById('post-tags').value = (data.tags || []).join(', ');
        document.getElementById('post-meta').value = data.meta_description || '';
        document.getElementById('save-status').textContent = (data.status || 'draft').toUpperCase();
        
        if (data.image) previewImage(data.image);
        if (data.content) quill.clipboard.dangerouslyPasteHTML(data.content);
    }

    function previewImage(url) {
        const img = document.getElementById('image-preview');
        const pl = document.getElementById('image-placeholder');
        if (url && (url.startsWith('http') || url.startsWith('/'))) {
            img.src = url;
            img.classList.remove('hidden');
            pl.classList.add('hidden');
        } else {
            img.classList.add('hidden');
            pl.classList.remove('hidden');
        }
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
        const data = {
            title,
            author: document.getElementById('post-author').value,
            category: document.getElementById('post-category').value,
            read_time: document.getElementById('post-read-time').value,
            status: status || 'draft',
            image: document.getElementById('post-image').value,
            tags,
            meta_description: document.getElementById('post-meta').value,
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
                window.history.replaceState({}, '', 'blog-editor.php?id=' + blogId);
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

    // [A3.10] Image upload wired to /php/api/upload.php
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
</script>

</body>
</html>
