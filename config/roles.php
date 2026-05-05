<?php
return [
    'normal' => ['login_status' => true, 'redirect_after_login' => 'photos.home', 'is_admin' => false],
    'admin' => ['login_status' => 'admin', 'redirect_after_login' => 'admin.dashboard.index', 'is_admin' => true],
    'admin_video' => ['login_status' => 'admin', 'redirect_after_login' => 'admin.videos.dashboard.index', 'is_admin' => true],
    'admin_vector' => ['login_status' => 'admin', 'redirect_after_login' => 'admin.vector.dashboard.index', 'is_admin' => true],
    'admin_image_editor' => ['login_status' => 'admin', 'redirect_after_login' => 'admin.contributors.submissions.index', 'is_admin' => true],
    'admin_video_editor' => ['login_status' => 'admin', 'redirect_after_login' => 'admin.videos.contributors.submissions.index', 'is_admin' => true],
    'admin_vector_editor' => ['login_status' => 'admin', 'redirect_after_login' => 'admin.vectors.contributors.submissions.index', 'is_admin' => true],
    'admin_super' => ['login_status' => 'admin', 'redirect_after_login' => 'admin.super.contact.index', 'is_admin' => true],
    'accountant' => ['login_status' => 'admin', 'redirect_after_login' => 'admin.accountant.downloads', 'is_admin' => true],
    'designer' => ['login_status' => 'admin', 'redirect_after_login' => 'admin.images.warehouse_remove_bg.index', 'is_admin' => true],
//    'admin_models'=>['redirect_after_login'=>'admin.models.dashboard.index'],
];