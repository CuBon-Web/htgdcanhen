<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'DONE'; //Return anything
});
Route::get('/crm', function () {
    return view('app');
});
// Route::get('/admin', function () {
//     dd(1);
//     return view('app');
// });
// API Routes for School Classes
Route::group(['prefix' => 'api', 'namespace' => 'Admin'], function () {
    Route::get('school-classes/active', 'SchoolClassController@getActive');
});

// Admin Routes for School Classes Management
Route::group(['prefix' => 'quan-ly-lop-hoc', 'namespace' => 'Admin', 'middleware' => 'CheckAuthClient'], function () {
    Route::get('/', 'SchoolClassController@index')->name('classes.index');
    Route::get('/them-moi', 'SchoolClassController@create')->name('classes.create');
    Route::post('/luu', 'SchoolClassController@store')->name('classes.store');
    Route::get('/{id}', 'SchoolClassController@show')->name('classes.show');
    Route::get('/{id}/sua', 'SchoolClassController@edit')->name('classes.edit');
    Route::put('/{id}', 'SchoolClassController@update')->name('classes.update');
    Route::delete('/{id}', 'SchoolClassController@destroy')->name('classes.destroy');
});

Route::get('/', 'HomeController@home')->name('home')->middleware(checkLanguage::class);
Route::group(['namespace' => 'Client', 'middleware' => ['checkLanguage']], function () {

    Route::get('chuyen-anh', 'QuizController@chuyenanh')->name('chuyenanh');

    Route::get('type-product/{id}', 'PageController@typeproduct');
    Route::get('district/{id}', 'PageController@district');
    Route::get('diem-review.html', 'PageController@diemReview')->name('diemReview');
    Route::post('ket-qua-tim-kiem', 'PageController@search')->name('search_result');
    Route::get('chon-tai-khoan.html', 'AuthController@ChoiseAccount')->name('ChoiseAccount')->middleware('CheckAuthLogout::class');

    // Đăng nhập
    Route::get('dang-nhap.html', 'AuthController@login')->name('login')->middleware('CheckAuthLogout::class');
    Route::post('dang-nhap.html', 'AuthController@postLogin')->name('postlogin');

    // Đăng ký
    Route::get('dang-ky.html', 'AuthController@register')->name('register');
    Route::post('dang-ky.html', 'AuthController@postRegister')->name('postRegister');

    // Quên mật khẩu
    Route::get('quen-mat-khau.html', 'AuthController@forgotPassword')->name('forgotPassword');
    Route::post('quen-mat-khau.html', 'AuthController@postForgotPassword')->name('postForgotPassword');

    // Đăng ký giáo viên
    Route::post('dang-ky-giao-vien.html', 'AuthController@postRegisterGiaovien')->name('postRegisterGiaovien');

    // Đăng xuất
    Route::get('dang-xuat.html', 'AuthController@logout')->name('logout')->middleware('CheckAuthClient::class');

    // Trang cá nhân
    Route::get('trang-ca-nhan.html', 'AuthController@profile')->name('profile')->middleware('CheckAuthClient::class');



    Route::group(['prefix' => 'crm-course'], function () {
        Route::get('khoa-hoc.html', 'AuthController@myCouseGiaoVien')->name('myCouseGiaoVien')->middleware('CheckAuthClient::class');
        // Route::get('tao-khoa-hoc.html','AuthController@taoKhoaHoc')->name('taoKhoaHoc')->middleware('CheckAuthClient::class');
        Route::get('dang-khoa-hoc-online.html', 'CouseController@postCouse')->name('postCouse')->middleware('CheckAuthClient::class');
        Route::post('dang-khoa-hoc-online.html', 'CouseController@postCourse')->name('postCourse')->middleware('CheckAuthClient::class');
        Route::get('chinh-sua-khoa-hoc-online/{id}.html', 'CouseController@editCouse')->name('editCouse')->middleware('CheckAuthClient::class');
        Route::get('xoa-khoa-hoc/{id}', 'CouseController@deleteCouse')->name('deleteCouse')->middleware('CheckAuthClient::class');
        Route::get('start-study/{id}.html', 'CouseController@startStudyCourse')->name('startStudyCourse')->middleware('CheckAuthClient::class');
        Route::get('baitap/{task_id}', 'BaitapController@getBaiTap')->middleware('CheckAuthClient::class');
        Route::get('tai-lieu-da-mua.html', 'AuthController@documentList')->name('documentListDamua')->middleware('CheckAuthClient::class');

        Route::group(['prefix'=>'hoc-sinh'], function(){
            Route::get('danh-sach.html','AuthController@listStudent')->name('listStudent')->middleware('CheckAuthClient::class');
            Route::get('chi-tiet/{id}.html','AuthController@studentDetail')->name('student.detail')->middleware('CheckAuthClient::class');
            Route::post('bulk-change-password','AuthController@bulkChangePassword')->name('bulkChangePassword')->middleware('CheckAuthClient::class');
            Route::post('bulk-delete','AuthController@bulkDeleteStudent')->name('bulkDeleteStudent')->middleware('CheckAuthClient::class');
            Route::post('bulk-restrict-access','AuthController@bulkRestrictAccess')->name('bulkRestrictAccess')->middleware('CheckAuthClient::class');
            Route::post('bulk-restrict-subscription','AuthController@bulkRestrictSubscription')->name('bulkRestrictSubscription')->middleware('CheckAuthClient::class');
            Route::post('bulk-change-subscription','AuthController@bulkChangeSubscription')->name('bulkChangeSubscription')->middleware('CheckAuthClient::class');
            Route::post('bulk-activate-subscription','AuthController@bulkActivateSubscription')->name('bulkActivateSubscription')->middleware('CheckAuthClient::class');
        });
        
    });
    Route::post('crm-course/game/save-result', 'GameController@saveResult')->name('game.save-result');
    Route::post('crm-course/game/check-result', 'GameController@checkResult')->name('game.check-result');
    Route::group(['prefix' => 'de-thi'], function () {
        Route::get('tat-ca.html', 'DethiController@allDeThi')->name('allDeThi');
        Route::post('load-more-exams', 'DethiController@loadMoreExams')->name('loadMoreExams');
        Route::post('them-vao-gio-hang', 'DethiController@themvaoGioHangDethi')->name('themvaoGioHangDethi');
        Route::get('danh-sach.html', 'DethiController@index')->name('khoiTaoDeThi')->middleware('CheckAuthClient::class');
        Route::get('tu-tao-de-thi.html', 'DethiController@tutaodethi')->name('tutaodethi')->middleware('CheckAuthClient::class');
        Route::get('chi-tiet-de-thi/{id}.html', 'DethiController@detail')->name('detailDeThi')->middleware('CheckAuthClient::class');
        Route::get('sua-de-thi/{id}.html', 'DethiController@edit')->name('editDeThi')->middleware('CheckAuthClient::class');
        Route::get('sua-cau-hoi/{id}.html', 'DethiController@editQuestion')->name('editQuestion')->middleware('CheckAuthClient::class');
        Route::post('sua-cau-hoi.html', 'DethiController@storeQuestion')->name('storeQuestion')->middleware('CheckAuthClient::class');
        Route::post('delete-question', 'DethiController@deleteQuestion')->middleware('CheckAuthClient::class');
        Route::get('upload-file-docx.html', 'DethiController@uploadFile')->name('uploadFile')->middleware('CheckAuthClient::class');
        Route::post('upload-file-docx.html', 'DethiController@PostuploadFile')->name('PostuploadFile')->middleware('CheckAuthClient::class');
        Route::post('store-exam', 'DethiController@storeExam')->middleware('CheckAuthClient::class');
        Route::post('folders', 'ExamFolderController@store')->name('exam-folders.store')->middleware('CheckAuthClient::class');
        Route::patch('folders/{folder}', 'ExamFolderController@update')->name('exam-folders.update')->middleware('CheckAuthClient::class');
        Route::delete('folders/{folder}', 'ExamFolderController@destroy')->name('exam-folders.destroy')->middleware('CheckAuthClient::class');
        Route::get('folders/tree', 'ExamFolderController@tree')->name('exam-folders.tree')->middleware('CheckAuthClient::class');
        Route::post('move-to-folder', 'DethiController@moveToFolder')->name('dethi.move-to-folder')->middleware('CheckAuthClient::class');
        Route::post('bulk-delete', 'DethiController@bulkDelete')->name('dethi.bulk-delete')->middleware('CheckAuthClient::class');
        Route::post('bulk-publish', 'DethiController@bulkPublish')->name('dethi.bulk-publish')->middleware('CheckAuthClient::class');
        Route::post('bulk-finish', 'DethiController@bulkFinish')->name('dethi.bulk-finish')->middleware('CheckAuthClient::class');
        Route::post('bulk-access', 'DethiController@bulkUpdateAccess')->name('dethi.bulk-access')->middleware('CheckAuthClient::class');
        Route::post('bulk-copy', 'DethiController@bulkCopy')->name('dethi.bulk-copy')->middleware('CheckAuthClient::class');
        Route::post('bulk-copy-session', 'DethiController@bulkCopySession')->name('dethi.bulk-copy-session')->middleware('CheckAuthClient::class');
        Route::post('bulk-cut', 'DethiController@bulkCut')->name('dethi.bulk-cut')->middleware('CheckAuthClient::class');
        Route::post('bulk-paste', 'DethiController@bulkPaste')->name('dethi.bulk-paste')->middleware('CheckAuthClient::class');
        Route::get('question-bank/data', 'DethiController@getQuestionBankData')->name('question-bank.data')->middleware('CheckAuthClient::class');
        Route::get('question-bank/folder/{folderId}/children', 'DethiController@getFolderChildren')->name('question-bank.folder.children')->middleware('CheckAuthClient::class');
        Route::get('question-bank/exam/{examId}/questions', 'DethiController@getExamQuestions')->name('question-bank.exam.questions')->middleware('CheckAuthClient::class');
        Route::post('filter-subject', 'DethiController@filterSubject');
        Route::post('filter-type', 'DethiController@filterType');
        Route::get('bat-dau-thi/{id}', 'DethiController@startTest')->name('startTest')->middleware('CheckAuthClient::class');
        Route::post('nop-bai-thi', 'DethiController@submitTest')->name('submitTest')->middleware('CheckAuthClient::class');
        Route::get('ket-qua-thi/{id}.html', 'DethiController@result')->name('resultDethi')->middleware('CheckAuthClient::class');
        Route::post('cham-diem-tu-luan', 'DethiController@gradeEssayQuestionsBulk')->name('gradeEssayQuestionsBulk')->middleware('CheckAuthClient::class');
        Route::get('cham-diem/{id}.html', 'DethiController@chamdiem')->name('chamdiem')->middleware('CheckAuthClient::class');
        Route::get('xoa-de-thi/{id}.html', 'DethiController@destroyDeThi')->name('destroyDeThi')->middleware('CheckAuthClient::class');
        Route::get('de-thi-theo-khoi-lop/{id}.html', 'DethiController@listCategorymainDethi')->name('listCategorymainDethi');
        Route::get('de-thi-theo-mon-hoc/{id}.html', 'DethiController@listCategoryDethi')->name('listCategoryDethi');
        Route::get('de-thi-theo-loai/{id}.html', 'DethiController@listBodDethi')->name('listBodDethi');

        Route::get('dang-ky-mua-de-thi/{id}', 'DethiController@dangkyMuaDeThi')->name('dangkyMuaDeThi')->middleware('CheckAuthClient::class');
        Route::match(['get', 'post'], 'download-score-table/{id}', 'DethiController@downloadScoreTable')->name('downloadScoreTable')->middleware('CheckAuthClient::class');
        Route::get('tai-xuong-de-thi/{id}', 'DethiController@cloneExam')->name('cloneExam')->middleware('CheckAuthClient::class');
        Route::get('xem-noi-dung-de-thi/{id}', 'DethiController@getExamContent')->name('getExamContent')->middleware('CheckAuthClient::class');
        Route::get('xuat-word-co-loi-giai/{id}', 'DethiController@exportWordWithAnswer')->name('exportWordWithAnswer')->middleware('CheckAuthClient::class');
        Route::get('xuat-word-khong-loi-giai/{id}', 'DethiController@exportWordWithoutAnswer')->name('exportWordWithoutAnswer')->middleware('CheckAuthClient::class');
    });
    Route::group(['prefix' => 'bai-tap'], function () {
        Route::post('folders', 'ExamFolderController@store')->name('baitap-folders.store')->middleware('CheckAuthClient::class');
        Route::patch('folders/{folder}', 'ExamFolderController@update')->name('baitap-folders.update')->middleware('CheckAuthClient::class');
        Route::delete('folders/{folder}', 'ExamFolderController@destroy')->name('baitap-folders.destroy')->middleware('CheckAuthClient::class');
        Route::get('folders/tree', 'ExamFolderController@tree')->name('baitap-folders.tree')->middleware('CheckAuthClient::class');
        Route::post('move-to-folder', 'BaitapController@moveToFolder')->name('baitap.move-to-folder')->middleware('CheckAuthClient::class');
        Route::post('bulk-delete', 'BaitapController@bulkDelete')->name('baitap.bulk-delete')->middleware('CheckAuthClient::class');
        Route::post('bulk-publish', 'BaitapController@bulkPublish')->name('baitap.bulk-publish')->middleware('CheckAuthClient::class');
        Route::post('bulk-finish', 'BaitapController@bulkFinish')->name('baitap.bulk-finish')->middleware('CheckAuthClient::class');
        Route::post('bulk-access', 'BaitapController@bulkUpdateAccess')->name('baitap.bulk-access')->middleware('CheckAuthClient::class');
        Route::post('bulk-copy-session', 'BaitapController@bulkCopySession')->name('baitap.bulk-copy-session')->middleware('CheckAuthClient::class');
        Route::post('bulk-cut', 'BaitapController@bulkCut')->name('baitap.bulk-cut')->middleware('CheckAuthClient::class');
        Route::post('bulk-paste', 'BaitapController@bulkPaste')->name('baitap.bulk-paste')->middleware('CheckAuthClient::class');
        Route::get('xem-noi-dung-bai-tap/{id}', 'BaitapController@getExerciseContent')->name('getExerciseContent')->middleware('CheckAuthClient::class');
        Route::get('tai-xuong-bai-tap/{id}', 'BaitapController@cloneBaitap')->name('cloneBaitap')->middleware('CheckAuthClient::class');
    });
    Route::group(['prefix' => 'tro-choi'], function () {
        Route::get('tao-game.html', 'GameController@create')->name('game.create')->middleware('CheckAuthClient::class');
        Route::get('danh-sach-game.html', 'GameController@listAll')->name('gamelistAll')->middleware('CheckAuthClient::class');
        Route::get('danh-sach-game/ai-la-trieu-phu-toan-hoc.html', 'GameController@listAITrieuPhuToanHoc')->name('gamelistAITrieuPhuToanHoc')->middleware('CheckAuthClient::class');
        Route::post('game/save-export', 'GameController@saveExport')->name('game.save-export')->middleware('CheckAuthClient::class');
        // Route::get('game/{gameExportId}', 'GameController@show')->name('game.show')->middleware('CheckAuthClient::class');
        Route::delete('game/delete/{id}', 'GameController@delete')->name('game.delete')->middleware('CheckAuthClient::class');
        Route::get('game/{gameExportId}/ranking', 'GameController@ranking')->name('game.ranking')->middleware('CheckAuthClient::class');
        Route::post('game/restart/{id}', 'GameController@restart')->name('game.restart')->middleware('CheckAuthClient::class');
        Route::get('upload-file-game.html', 'GameController@uploadFile')->name('uploadFileGame')->middleware('CheckAuthClient::class');
        Route::get('bat-dau-game/{id}.html', 'GameController@startGame')->name('startGame')->middleware('CheckAuthClient::class');
        Route::get('game/{gameId}/question', 'GameController@getQuestion')->name('game.get-question');
        Route::post('game/{gameId}/submit-answer', 'GameController@submitAnswer')->name('game.submit-answer');
        Route::post('game/{gameId}/get-correct-answer', 'GameController@getCorrectAnswer')->name('game.get-correct-answer');
        Route::post('game/{gameId}/save-result', 'GameController@saveGameResult')->name('game.save-game-result');
        
        // Quản lý quà tặng
        Route::get('qua-tang/danh-sach.html', 'GameController@rewardIndex')->name('game.reward.index')->middleware('CheckAuthClient::class');
        Route::get('qua-tang/them-moi.html', 'GameController@rewardCreate')->name('game.reward.create')->middleware('CheckAuthClient::class');
        Route::post('qua-tang/luu.html', 'GameController@rewardStore')->name('game.reward.store')->middleware('CheckAuthClient::class');
        Route::get('qua-tang/{id}/sua.html', 'GameController@rewardEdit')->name('game.reward.edit')->middleware('CheckAuthClient::class');
        Route::put('qua-tang/{id}/cap-nhat.html', 'GameController@rewardUpdate')->name('game.reward.update')->middleware('CheckAuthClient::class');
        Route::delete('qua-tang/{id}/xoa.html', 'GameController@rewardDelete')->name('game.reward.delete')->middleware('CheckAuthClient::class');
        
        // Cấu hình phần thưởng cho game
        Route::get('game/{gameId}/cau-hinh-phan-thuong.html', 'GameController@rewardConfigIndex')->name('game.reward.config.index')->middleware('CheckAuthClient::class');
        Route::get('game/{gameId}/cau-hinh-phan-thuong/data.html', 'GameController@rewardConfigData')->name('game.reward.config.data')->middleware('CheckAuthClient::class');
        Route::post('game/{gameId}/cau-hinh-phan-thuong/luu.html', 'GameController@rewardConfigStore')->name('game.reward.config.store')->middleware('CheckAuthClient::class');
        Route::delete('game/cau-hinh-phan-thuong/{id}/xoa.html', 'GameController@rewardConfigDelete')->name('game.reward.config.delete')->middleware('CheckAuthClient::class');
        Route::get('game/{gameId}/my-result', 'GameController@myResult')->name('game.my-result')->middleware('CheckAuthClient::class');
        Route::get('bang-xep-hang-tong-quat', 'GameController@overallRanking')->name('game.overall-ranking')->middleware('CheckAuthClient::class');
        Route::get('bang-xep-hang-game.html', 'GameController@shareRanking')->name('game.share-ranking');
        Route::get('game/{gameId}/ranking-view', 'GameController@publicGameRanking')->name('game.public-ranking');
        Route::get('de-thi/{dethiId}/score-view', 'DethiController@publicScoreTable')->name('dethi.public-score');
        Route::delete('xoa-ket-qua-hoc-sinh/{studentId}', 'GameController@deleteStudentResults')->name('game.delete-student-results')->middleware('CheckAuthClient::class');
        Route::delete('xoa-nhieu-ket-qua-hoc-sinh', 'GameController@deleteMultipleStudentResults')->name('game.delete-multiple-student-results')->middleware('CheckAuthClient::class');
    });
    Route::group(['prefix' => 'bai-tap'], function () {
        Route::get('danh-sach.html', 'BaitapController@index')->name('danhSachBaiTap')->middleware('CheckAuthClient::class');
        Route::get('upload-file-docx.html', 'BaitapController@uploadFile')->name('uploadFileBaitap')->middleware('CheckAuthClient::class');
        Route::post('move-to-folder', 'BaitapController@moveToFolder')->name('baitap.move-to-folder')->middleware('CheckAuthClient::class');
        Route::post('nop-bai-thi', 'BaitapController@submitBaitap')->name('submitBaitap')->middleware('CheckAuthClient::class');
    });
    Route::group(['prefix' => 'trang-html', 'middleware' => ['CheckAuthClient::class']], function () {
        Route::get('danh-sach.html', 'HtmlPageController@index')->name('html-pages.index');
        Route::get('them-moi.html', 'HtmlPageController@create')->name('html-pages.create');
        Route::post('luu.html', 'HtmlPageController@store')->name('html-pages.store');
        Route::get('{htmlPage}/sua.html', 'HtmlPageController@edit')->name('html-pages.edit');
        Route::put('{htmlPage}/cap-nhat.html', 'HtmlPageController@update')->name('html-pages.update');
        Route::delete('{htmlPage}/xoa.html', 'HtmlPageController@destroy')->name('html-pages.destroy');
    });
    Route::get('chinh-sua-trang-ca-nhan.html', 'AuthController@editProfile')->name('chinhSuaTrangCaNhan')->middleware('CheckAuthClient::class');
    Route::post('chinh-sua-trang-ca-nhan.html', 'AuthController@postShowProfile')->name('postchinhSuaTrangCaNhan')->middleware('CheckAuthClient::class');
    Route::post('chinh-sua-mat-khau.html', 'AuthController@postChangePassword')->name('postChangePassword')->middleware('CheckAuthClient::class');
    Route::get('tu-lieu.html', 'PageController@tulieu')->name('tulieu');
    Route::get('trang-noi-dung/{slug}.html', 'PageContentController@detail')->name('pagecontent');
    Route::get('xem-trang-html/{slug}.html', 'HtmlPageController@showPublic')->name('html-pages.public');

    Route::group(['prefix' => 'tai-lieu'], function () {
        Route::post('them-vao-gio-hang', 'DocumentController@themvaoGioHangTailieu')->name('themvaoGioHangTailieu');
        Route::get('{danhmuc}.html', 'DocumentController@documentList')->name('documentList');
        Route::get('{danhmuc}/{slug}.html', 'DocumentController@documentDetail')->name('documentDetail');
        Route::get('/dang-ky/{id}/{slug}.html', 'DocumentController@dangkytailieu')->name('dangkytailieu')->middleware('CheckAuthClient::class');
    });

    Route::get('ket-qua-bai-test/{id}', 'CouseController@ketquatailieu')->name('ketquatailieu')->middleware('CheckAuthClient::class');

    // Route::get('khoa-hoc/danh-muc/{slug}.html','PageController@serviceCateList')->name('serviceCateList');
    // Route::get('khoa-hoc.html','PageController@serviceList')->name('serviceList');

    // Route::get('cham-diem/{id}.html','CouseController@teacherChamdiem')->name('teacherChamdiem')->middleware('CheckAuthClient::class');
    // Route::post('cham-diem','CouseController@postteacherChamdiem')->name('postteacherChamdiem')->middleware('CheckAuthClient::class');


    Route::get('khoa-hoc/{slug}.html', 'PageController@serviceDetail')->name('serviceDetail');


    Route::get('gioi-thieu.html', 'PageController@aboutUs')->name('aboutUs');
    Route::get('cong-nghe.html', 'PageController@technology')->name('technology');
    Route::get('lien-he.html', 'PageController@contact')->name('lienHe');
    Route::post('lien-he', 'PageController@postcontact')->name('postcontact');
    Route::get('du-an-tieu-bieu.html', 'PageController@duanTieuBieu')->name('duanTieuBieu');
    Route::get('du-an-tieu-bieu/{slug}.html', 'PageController@duanTieuBieuDetail')->name('duanTieuBieuDetail');
    Route::get('cau-hoi-thuong-gap.html', 'PageController@fag')->name('fag');


    Route::group(['prefix' => 'cong-trinh'], function () {
        Route::get('/tat-ca.html', 'ConstructionController@list')->name('allListConstruction');
        Route::get('{id}.html', 'ConstructionController@detail')->name('detailConstruction');
    });
    Route::get('quickview/{id}', 'PageController@quickview')->name('quickview');
    Route::get('bang-gia.html', 'PageController@baogia')->name('banggia');

    Route::get('gio-hang.html', 'CartController@listCart')->name('listCart');
    Route::post('add-to-cart', 'CartController@addToCart')->name('add.to.cart');
    Route::post('update-cart', 'CartController@update')->name('update.cart');
    Route::post('remove-from-cart', 'CartController@remove')->name('remove.from.cart');
    Route::get('cart-count', 'CartController@cartCount')->name('cart.count');
    Route::get('thanh-toan.html', 'CartController@checkout')->name('checkout');
    Route::get('thantoan', 'CartController@postBill')->name('postBill');

    Route::get('dat-ban.html', 'PageController@orderNow')->name('orderNow');
    Route::get('menu.html', 'PageController@menu')->name('menu');
    Route::get('account/orders', 'AuthController@accoungOrder')->name('accoungOrder')->middleware('CheckAuthClient::class');
    Route::get('account/orders/{billid}', 'AuthController@accoungOrderDetail')->name('accoungOrderDetail')->middleware('CheckAuthClient::class');

    Route::get('auth/google', 'GoogleController@redirectToGoogle')->name('loginGoogle');
    Route::get('auth/google/callback', 'GoogleController@handleGoogleCallback');

    Route::get('auth/facebook', 'FacebookController@redirectToFacebook')->name('loginFacebook');
    Route::get('auth/facebook/callback', 'FacebookController@handleFacebookCallback');
    Route::group(['prefix' => 'tin-tuc'], function () {
        Route::get('/tat-ca.html', 'BlogController@list')->name('allListBlog');
        Route::get('danh-muc/{slug}.html', 'BlogController@listCateBlog')->name('listCateBlog');
        Route::get('loai-danh-muc/{slug}.html', 'BlogController@listTypeBlog')->name('listTypeBlog');
        Route::get('chi-tiet/{slug}.html', 'BlogController@detailBlog')->name('detailBlog');
    });
    Route::get('lich-khai-giang.html', 'SolutionController@list')->name('listSolution');
    Route::get('lich-khai-giang/{slug}.html', 'SolutionController@detail')->name('detailSolution');

    Route::get('giao-vien.html', 'PageController@listTeacher')->name('listTeacher');
    Route::get('giao-vien/{slug}.html', 'PageController@detailTeacher')->name('detailTeacher');




    Route::post('khoa-hoc-online/them-vao-gio-hang', 'CouseController@themvaoGioHangKhoaHoc')->name('themvaoGioHangKhoaHoc');
    Route::get('khoa-hoc-online/dang-ky/{id}/{slug}.html', 'CouseController@dangkykhoahoc')->name('dangkykhoahoc')->middleware('CheckAuthClient::class');
    Route::get('khoa-hoc-online.html', 'CouseController@couseList')->name('couseList');
    Route::post('khoa-hoc-online/load-more', 'CouseController@loadMoreCourses')->name('loadMoreCourses');
    Route::get('khoa-hoc-online/danh-muc/{id}.html', 'CouseController@listCategoryMainCourse')->name('listCategoryMainCourse');
    Route::get('khoa-hoc-online/loai/{id}.html', 'CouseController@listTypeCourse')->name('listTypeCourse');
    Route::get('khoa-hoc-online/{cate_slug}.html', 'CouseController@couseListCate')->name('couseListCate');
    Route::get('khoa-hoc-online/{cate_slug}/{type_slug}.html', 'CouseController@couseListType')->name('couseListType');
    Route::get('khoa-hoc-online/{cate_slug}/{type_slug}/{type_two_slug}.html', 'CouseController@couseListTypeTwo')->name('couseListTypeTwo');
    Route::get('chi-tiet-khoa-hoc-online/{slug}.html', 'CouseController@couseDetail')->name('couseDetail');








    Route::get('tat-ca-san-pham.html', 'ProductController@allProduct')->name('allProduct');
    Route::get('chi-tiet/{cate}/{type}/{id}.html', 'ProductController@detail_product')->name('detailProduct');
    Route::get('{danhmuc}.html', 'ProductController@allListCate')->name('allListProCate');
    Route::get('{danhmuc}/{loaidanhmuc}.html', 'ProductController@allListType')->name('allListType');
    Route::get('{danhmuc}/{loaidanhmuc}/{thuonghieu}.html', 'ProductController@allListTypeTwo')->name('allListTypeTwo');
});


Route::post('/languages', 'LanguageController@index')->name('languages');
