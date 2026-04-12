<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<style>
  body { margin:0; padding:0; background:#f1f5f9; font-family:'Segoe UI',Arial,sans-serif; }
  .wrap { max-width:600px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
  .header { background:#ec5b13; padding:36px 40px; text-align:center; }
  .header img { height:36px; }
  .header h1 { color:#fff; margin:16px 0 0; font-size:22px; font-weight:700; }
  .body { padding:40px; }
  .body p { color:#475569; font-size:15px; line-height:1.7; margin:0 0 16px; }
  .btn { display:inline-block; background:#ec5b13; color:#fff !important; text-decoration:none; padding:14px 32px; border-radius:10px; font-weight:700; font-size:15px; margin:8px 0 24px; }
  .note { background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:14px 18px; font-size:13px; color:#9a3412; margin:20px 0 0; }
  .footer { background:#f8fafc; padding:24px 40px; text-align:center; font-size:12px; color:#94a3b8; border-top:1px solid #e2e8f0; }
  .url-fallback { word-break:break-all; color:#64748b; font-size:12px; margin-top:8px; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Verify Your Email Address</h1>
  </div>
  <div class="body">
    <p>Hi <strong><?php echo htmlspecialchars($booking['name'] ?? 'there'); ?></strong>,</p>
    <p>Thanks for creating an account on <strong>CSN Explore</strong>. To activate your account and start exploring, please verify your email address by clicking the button below.</p>
    <p style="text-align:center">
      <a href="<?php echo htmlspecialchars($booking['verifyLink']); ?>" class="btn">Verify My Email</a>
    </p>
    <p class="url-fallback">Or copy and paste this link into your browser:<br/><?php echo htmlspecialchars($booking['verifyLink']); ?></p>
    <div class="note">
      ⏱ This link expires in <strong>24 hours</strong>. If you didn't create an account, you can safely ignore this email.
    </div>
  </div>
  <div class="footer">
    &copy; <?php echo date('Y'); ?> CSN Explore &mdash; Chhatrapati Sambhajinagar's Travel Platform
  </div>
</div>
</body>
</html>
