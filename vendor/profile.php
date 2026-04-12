<?php
$vendor_page  = 'profile';
$vendor_title = 'My Profile | Vendor Portal';
require 'vendor-header.php';
?>

<style>
.page-header h1 { font-size: 28px; margin-bottom: 5px; }
.page-header p { font-size: 14px; color: #666; }
.profile-container { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; }
.profile-card { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center; }
.profile-avatar { width: 80px; height: 80px; background: linear-gradient(135deg, #ec5b13, #ff8c42); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 32px; margin: 0 auto 15px; }
.profile-name { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 5px; }
.profile-username { font-size: 14px; color: #666; margin-bottom: 15px; }
.profile-info { text-align: left; }
.info-item { padding: 10px 0; border-bottom: 1px solid #eee; }
.info-item:last-child { border-bottom: none; }
.info-label { font-size: 12px; color: #999; font-weight: 500; }
.info-value { font-size: 14px; color: #333; margin-top: 3px; }
.form-section { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
.form-section h2 { font-size: 18px; margin-bottom: 15px; color: #333; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; font-size: 14px; font-weight: 500; color: #333; margin-bottom: 5px; }
.form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: Arial, sans-serif; }
.form-group input:focus { outline: none; border-color: #ec5b13; box-shadow: 0 0 0 2px rgba(236, 91, 19, 0.1); }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.btn { background: #ec5b13; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500; }
.btn:hover { background: #d94a0f; }
.btn-secondary { background: #666; }
.btn-secondary:hover { background: #555; }
.btn-danger { background: #dc3545; }
.btn-danger:hover { background: #c82333; }
.danger-zone { background: #fff5f5; border: 1px solid #ffcccc; padding: 20px; border-radius: 5px; margin-top: 20px; }
.danger-zone h3 { color: #dc3545; margin-bottom: 10px; }
.danger-zone p { font-size: 14px; color: #666; margin-bottom: 15px; }
@media (max-width: 768px) {
    .profile-container { grid-template-columns: 1fr; }
}
</style>

<div class="page-header">
    <h1>My Profile</h1>
    <p>Manage your account details and security</p>
</div>

<div class="profile-container">
    <!-- Profile Info Card -->
    <div class="profile-card">
        <div class="profile-avatar" id="profile-avatar">V</div>
        <div class="profile-name" id="profile-name">Vendor Name</div>
        <div class="profile-username" id="profile-username">@username</div>
        <div class="profile-info">
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value" id="profile-email">—</div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value" id="profile-phone">—</div>
            </div>
            <div class="info-item">
                <div class="info-label">Business Name</div>
                <div class="info-value" id="profile-business">—</div>
            </div>
            <div class="info-item">
                <div class="info-label">Member Since</div>
                <div class="info-value" id="profile-joined">—</div>
            </div>
        </div>
    </div>

    <!-- Edit Forms -->
    <div>
        <!-- Edit Profile -->
        <div class="form-section">
            <h2>Edit Profile</h2>
            <form id="profile-form" onsubmit="saveProfile(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="p-name" required>
                    </div>
                    <div class="form-group">
                        <label>Business Name</label>
                        <input type="text" id="p-business">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="p-email">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" id="p-phone">
                    </div>
                </div>
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="form-section">
            <h2>Change Password</h2>
            <form id="password-form" onsubmit="changePassword(event)">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" id="pw-current" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>New Password (min 8 characters)</label>
                        <input type="password" id="pw-new" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" id="pw-confirm" required minlength="8">
                    </div>
                </div>
                <button type="submit" class="btn">Update Password</button>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="danger-zone">
            <h3>Sign Out</h3>
            <p>Sign out of your vendor account. You'll need to log in again to access your dashboard.</p>
            <button onclick="vendorLogout()" class="btn btn-danger">Sign Out</button>
        </div>
    </div>
</div>

</div> <!-- End main-content -->

<script>
const BASE = '<?php echo VENDOR_API_BASE; ?>';

async function loadProfile() {
    const data = await vendorApi(`${BASE}/php/api/vendor-profile.php?action=get`);
    if (!data?.vendor) return;
    
    const v = data.vendor;
    const name = v.name || 'Vendor';
    
    document.getElementById('profile-avatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('profile-name').textContent = name;
    document.getElementById('profile-username').textContent = '@' + (v.username || '');
    document.getElementById('profile-email').textContent = v.email || '—';
    document.getElementById('profile-phone').textContent = v.phone || '—';
    document.getElementById('profile-business').textContent = v.business_name || '—';
    document.getElementById('profile-joined').textContent = (v.created_at || '').substring(0, 10);
    
    document.getElementById('p-name').value = v.name || '';
    document.getElementById('p-business').value = v.business_name || '';
    document.getElementById('p-email').value = v.email || '';
    document.getElementById('p-phone').value = v.phone || '';
}

async function saveProfile(e) {
    e.preventDefault();
    const data = await vendorApi(`${BASE}/php/api/vendor-profile.php?action=update`, {
        method: 'POST',
        body: JSON.stringify({
            name: document.getElementById('p-name').value.trim(),
            business_name: document.getElementById('p-business').value.trim(),
            email: document.getElementById('p-email').value.trim(),
            phone: document.getElementById('p-phone').value.trim(),
        })
    });
    
    if (data?.success) {
        showToast('Profile updated successfully', 'success');
        loadProfile();
    } else {
        showToast(data?.error || 'Failed to update profile', 'error');
    }
}

async function changePassword(e) {
    e.preventDefault();
    const newPw = document.getElementById('pw-new').value;
    const confirmPw = document.getElementById('pw-confirm').value;
    
    if (newPw !== confirmPw) {
        showToast('Passwords do not match', 'error');
        return;
    }
    
    const data = await vendorApi(`${BASE}/php/api/vendor-profile.php?action=change_password`, {
        method: 'POST',
        body: JSON.stringify({
            current_password: document.getElementById('pw-current').value,
            new_password: newPw
        })
    });
    
    if (data?.success) {
        showToast('Password updated! Please log in again.', 'success');
        document.getElementById('password-form').reset();
        setTimeout(vendorLogout, 2000);
    } else {
        showToast(data?.error || 'Failed to change password', 'error');
    }
}

loadProfile();
</script>

<?php require 'vendor-footer.php'; ?>
