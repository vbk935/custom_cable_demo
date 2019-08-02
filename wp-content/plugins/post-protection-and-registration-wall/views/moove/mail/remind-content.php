<?php if ( empty( get_option( 'moove_protection-email' )['Remindcontent'] ) ): ?>

<h1>Dear [[client_name]]</h1>
<br />
<p>Someone, with your e-mail address ([[client_email]]) has requested a password reset on our site, [[site_url]]</p>
<p>If you didn't request this, please ignore this message.</p>
<p>If you have started this password reset operation and wish to continue, please click on the link below to do so:</p>
<p><a href="[[reset_link]]">[[reset_link]]</a></p>
<p>Regards,
<br />
[[blog_name]]
</p>

<?php else:
    echo get_option( 'moove_protection-email' )['Remindcontent'];
endif; ?>

