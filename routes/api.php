<?php

use Illuminate\Http\Request;

use Spatie\Analytics\Period;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('uploadcourse', 'Api\AllController@uploadCourser');
Route::post('parse-content', 'Client\DethiController@parseContent');
Route::post('parse-content-question', 'Client\DethiController@parseContentQuestion');
Route::post('upload-image-khoahoc', 'Api\AllController@uploadImageCourser');
Route::post('store_file','Api\AllController@fileStore');
Route::post('import_excel','Api\AllController@importExcel');
Route::post('import_excel_docs','Api\AllController@importExcelDocs');
Route::get('export-excel-customer','Api\AllController@exportExcelCustomer');
Route::post('upload-audio','Api\AllController@uploadAudio');
Route::post('upload-question-image','Api\AllController@uploadQuestionImage');
Route::post('upload-file-docx','Client\ExamcoeController@upload')->name('PostuploadFile');
Route::group(['namespace'=>'Api','middleware' => 'api'],function(){
	Route::post('login','AuthController@login');
	Route::post('upload-image','AllController@uploadImage');
	Route::post('upload-image-multi','AllController@uploadImageMulti');
	
});
Route::group(['namespace'=>'Api','middleware'=>'auth:api'],function(){

	Route::get('/rolee', function () {
		$user = request()->user();
		dd($user->can('view-users'));
		return view('welcome');
	})->middleware('auth');

	

	Route::post('logout','AuthController@logout'); 
	Route::get('getNotification','NotificationController@get');
	Route::get('profile','AuthController@authentication');
	Route::post('changepass','AuthController@changePass');
	Route::group(['prefix'=>'menu'],function(){
		Route::get('listMenu','MenuController@listMenu');
		Route::get('getAllMenu','MenuController@getAllMenu');
		Route::post('saveChangeMenu','MenuController@saveChangemenu');
		Route::post('addNewMenu','MenuController@addNewMenu');
		Route::get('getEditMenu/{id}','MenuController@getEditMenu');
		Route::post('saveEditMenuById/{id}','MenuController@saveEditMenuById');
		Route::get('deleteMenuById/{id}','MenuController@deleteMenuById');
	});
	Route::group(['prefix' => 'language'], function () {
		Route::post('detailLanguage', 'LanguageController@detailLanguage')->name('language.detail');
		Route::post('saveLanguage', 'LanguageController@saveLanguage')->name('language.save');
		Route::post('searchLanguage', 'LanguageController@searchLanguage')->name('language.search');
		Route::post('activeLanguage', 'LanguageController@getActiveLanguage')->name('language.active');
		Route::post('saveLanguageStatic', 'LanguageController@saveLanguageStatic')->name('language.saveLanguageStatic');
		Route::post('searchLanguageStatic', 'LanguageController@searchLanguageStatic')->name('language.searchLanguageStatic');
		Route::post('saveLanguageStaticByLang', 'LanguageController@saveLanguageStaticByLang')->name('language.saveLanguageStaticByLang');
		Route::get('deleteLanguage/{code}', 'LanguageController@deleteLanguage')->name('language.delete');
	}); 
	Route::group(['prefix'=>'document','namspace'=>"document"], function(){
		Route::post('list','DocumentController@list');
		Route::post('create','DocumentController@create');
		Route::get('edit/{id}','DocumentController@edit');
		Route::get('delete/{id}','DocumentController@delete');
		Route::group(['prefix'=>'category'], function(){
			Route::post('add','DocumentCateController@add');
			Route::post('list','DocumentCateController@list');
			Route::get('edit/{id}','DocumentCateController@edit');
			Route::get('delete/{id}','DocumentCateController@delete');
		});
	});
	Route::group(['prefix'=>'document','namespace'=>'Bill'], function(){
		Route::post('draft','DocumentController@draft');
		Route::get('detail/{code}','DocumentController@detail');
		Route::post('change-status','DocumentController@changeStatus');
	});
	Route::group(['prefix'=>'bill','namespace'=>'Bill'], function(){
		Route::get('list','BillController@list');
		Route::post('add','BillController@add');
		Route::post('draft','BillController@draft');
		Route::get('detail/{code}','BillController@detail');
		Route::post('change-status','BillController@changeStatus');
	});
	Route::group(['prefix'=>'pagecontent'], function(){
		Route::post('add','PageContentController@add');
	});
	Route::group(['prefix'=>'messcontact'], function(){
		Route::post('list','AllController@listMesscontact');
	});
	Route::post('addLibrary','AllController@addLibrary');
	Route::group(['prefix'=>'entrance'], function(){
		Route::post('list','EntranceController@list');
		Route::get('edit/{code}','EntranceController@detail');
		Route::get('delete/{id}','EntranceController@delete');
	});
	Route::group(['prefix'=>'dethi','namespace'=>'Bill'], function(){
		Route::post('draft','DethiController@draft');
		Route::get('detail/{code}','DethiController@detail');
		Route::post('change-status','DethiController@changeStatus');
	});
	Route::group(['prefix'=>'quanlydethi'], function(){
		Route::post('add','DethiController@addDethi');
		Route::post('list','DethiController@listDethi');
		Route::get('delete/{id}','DethiController@deleteDethi');
		Route::get('edit/{id}','DethiController@editDethi');
		Route::post('change-status','DethiController@changeStatusDethi');
		Route::post('delete-array-id','DethiController@deleteDethiArrayId');
	});
	Route::group(['prefix'=>'course','namespace'=>'Bill'], function(){
		// Route::get('list','BillController@list');
		// Route::post('add','BillController@add');
		Route::post('draft','CourseController@draft');
		Route::get('detail/{code}','CourseController@detail');
		Route::post('change-status','CourseController@changeStatus');

		Route::group(['prefix'=>'cate'], function(){
			Route::post('add','CourseController@addCateDocument');
			Route::post('list','CourseController@listCateDocument');
			Route::get('delete/{id}','CourseController@deleteCateDocument');
			Route::get('edit/{id}','CourseController@editCateDocument');
		});
		Route::group(['prefix'=>'document'], function(){
			Route::post('add','CourseController@addDocument');
			Route::post('list','CourseController@listDocument');
			Route::get('delete/{id}','CourseController@deleteDocument');
			Route::get('edit/{id}','CourseController@editDocument');
		});
	});

	Route::group(['prefix'=>'quiz', 'namespace'=>'Quiz'], function(){
		Route::group(['prefix'=>'categorymain'], function(){
			Route::get('find/{id}','CategoryMainController@findCategory');
			Route::post('add','CategoryMainController@add');
			Route::post('list','CategoryMainController@list');
			Route::get('delete/{id}','CategoryMainController@delete');
			Route::get('edit/{id}','CategoryMainController@edit');
		});
		Route::group(['prefix'=>'category'], function(){
			Route::post('add','QuizCategoryController@add');
			Route::post('list','QuizCategoryController@list');
			Route::get('delete/{id}','QuizCategoryController@delete');
			Route::get('edit/{id}','QuizCategoryController@edit');
		});
		Route::group(['prefix'=>'typecategory'], function(){
			Route::post('add','TypeCategoryController@add');
			Route::post('list','TypeCategoryController@list');
			Route::get('delete/{id}','TypeCategoryController@delete');
			Route::get('edit/{id}','TypeCategoryController@edit');
		});
		


	});



	Route::group(['prefix'=>'docs', 'namespace'=>'Docs'], function(){
		
		Route::group(['prefix'=>'categorymain'], function(){
			Route::post('add','CategoryMainController@add');
			Route::post('list','CategoryMainController@list');
			Route::get('delete/{id}','CategoryMainController@delete');
			Route::get('edit/{id}','CategoryMainController@edit');
		});

		Route::group(['prefix'=>'category'], function(){
			Route::post('add','DocsCategoryController@add');
			Route::post('list','DocsCategoryController@list');
			Route::get('delete/{id}','DocsCategoryController@delete');
			Route::get('edit/{id}','DocsCategoryController@edit');
		});
		Route::group(['prefix'=>'exam'], function(){
			Route::post('add','ExamController@add');
			Route::post('list','ExamController@list');
			Route::get('delete/{id}','ExamController@delete');
			Route::get('edit/{id}','ExamController@edit');
		});
		Route::group(['prefix'=>'part'], function(){
			Route::post('add','PartController@add');
			Route::post('list','PartController@list');
			Route::get('delete/{id}','PartController@delete');
			Route::get('edit/{id}','PartController@edit');
		});
		Route::group(['prefix'=>'question'], function(){
			Route::post('add','QuestionsController@add');
			Route::post('list','QuestionsController@list');
			Route::get('delete/{id}','QuestionsController@delete');
			Route::get('edit/{id}','QuestionsController@edit');
			Route::get('findPartExam/{exam_id}','QuestionsController@findPart');
			Route::post('findQuestionPartExam','QuestionsController@findQuestionPartExam');
		});
		Route::group(['prefix'=>'question_group'], function(){
			Route::post('add','QuestionsGroupController@add');
			Route::post('list','QuestionsGroupController@list');
			Route::get('delete/{id}','QuestionsGroupController@delete');
			Route::get('edit/{id}','QuestionsGroupController@edit');
			Route::get('findQuestionPart/{exam_id}/{part_id}','QuestionsGroupController@findQuestionPart');
			Route::get('findGroupQuestion/{exam_id}','QuestionsGroupController@findGroupQuestion');
		});
	});




	Route::group(['prefix'=>'product', 'namespace'=>'Product'], function(){
		Route::post('create','ProductController@create');
		Route::post('list','ProductController@list');
		Route::get('edit/{id}','ProductController@edit');
		Route::get('delete/{id}','ProductController@delete');

		Route::post('create-test','ProductController@createTest');
		Route::post('list-test','ProductController@listTest');
		Route::get('edit-test/{id}','ProductController@editTest');

		Route::group(['prefix'=>'category'], function(){
			Route::post('add','CategoryController@add');
			Route::post('list','CategoryController@list');
			Route::get('delete/{id}','CategoryController@delete');
			Route::get('edit/{id}','CategoryController@edit');
		});
		Route::group(['prefix'=>'product_type'], function(){
			Route::post('add','TypeProductController@add');
			Route::post('list','TypeProductController@list');
			Route::get('delete/{id}','TypeProductController@delete');
			Route::get('edit/{id}','TypeProductController@edit');
			Route::get('findCateType/{cate_id}','TypeProductController@findType');
		});
		Route::group(['prefix'=>'type_two'], function(){
			Route::post('add','TypeTwoProductController@add');
			Route::post('list','TypeTwoProductController@list');
			Route::get('delete/{id}','TypeTwoProductController@delete');
			Route::get('edit/{id}','TypeTwoProductController@edit');
			Route::get('findCateType/{cate_id}','TypeTwoProductController@findType');
		});
	});
	Route::group(['prefix'=>'construction','namspace'=>"Construction"], function(){
		Route::post('list','Construction\ConstructionController@list');
		Route::post('create','Construction\ConstructionController@create');
		Route::get('edit/{id}','Construction\ConstructionController@edit');
		Route::get('listPro','Construction\ConstructionController@listProduct');
	});
	Route::group(['prefix'=>'solution','namspace'=>"Solution"], function(){
		Route::post('list','SolutionController@list');
		Route::post('create','SolutionController@create');
		Route::get('edit/{id}','SolutionController@edit');
		Route::get('delete/{id}','SolutionController@delete');
	});
	Route::group(['prefix'=>'project','namspace'=>"Project"], function(){
		Route::post('list','ProjectController@list');
		Route::post('create','ProjectController@create');
		Route::get('edit/{id}','ProjectController@edit');
		Route::get('delete/{id}','ProjectController@delete');
	});
	Route::group(['prefix'=>'promotion','namspace'=>"Promotion"], function(){
		Route::post('list','PromotionController@list');
		Route::post('create','PromotionController@create');
		Route::get('edit/{id}','PromotionController@edit');
		Route::get('delete/{id}','PromotionController@delete');
	});
	Route::group(['prefix'=>'service','namspace'=>"service"], function(){
		Route::post('list','ServiceController@list');
		Route::post('create','ServiceController@create');
		Route::get('edit/{id}','ServiceController@edit');
		Route::get('delete/{id}','ServiceController@delete');
		Route::group(['prefix'=>'category'], function(){
			Route::post('add','ServiceCateController@add');
			Route::post('list','ServiceCateController@list');
			Route::get('edit/{id}','ServiceCateController@edit');
			Route::get('delete/{id}','ServiceCateController@delete');
		});
	});


	Route::post('listAdsbanner','BannerAdsController@list');
	Route::post('createAdsbanner','BannerAdsController@create');
	Route::get('editAdsbanner/{id}','BannerAdsController@edit');
	Route::get('deleteAdsbanner/{id}','BannerAdsController@delete');

	Route::group(['prefix'=>'reviewCus','namspace'=>"reviewCus"], function(){
		Route::post('list','ReviewCusController@list');
		Route::post('create','ReviewCusController@create');
		Route::get('edit/{id}','ReviewCusController@edit');
		Route::get('delete/{id}','ReviewCusController@delete');
	});
	Route::group(['prefix'=>'blog', 'namespace'=>'Blog'], function(){
		Route::post('create','BlogController@create');
		Route::post('list','BlogController@list');
		Route::get('edit/{id}','BlogController@edit');
		Route::get('delete/{id}','BlogController@delete');
		Route::group(['prefix'=>'category'], function(){
			Route::post('add','BlogCategoryController@add');
			Route::post('list','BlogCategoryController@list');
			Route::get('edit/{id}','BlogCategoryController@edit');
			Route::get('delete/{id}','BlogCategoryController@deleteCateBlog');
		});
		Route::group(['prefix'=>'type'], function(){
			Route::post('add','BlogTypegoryController@add');
			Route::post('list','BlogTypegoryController@list');
			Route::get('edit/{id}','BlogTypegoryController@edit');
			Route::get('delete/{id}','BlogTypegoryController@deleteTypeBlog');
			Route::get('findtype/{id}','BlogTypegoryController@findTypeBlog');
		});
	});
	Route::group(['prefix'=>'page_content'], function(){
		Route::post('create','PageContentController@create');
		Route::post('list','PageContentController@list');
		Route::get('edit/{quiz_id}/{language}','PageContentController@edit');
		Route::get('delete/{quiz_id}','PageContentController@deletePageContent');
	});
	Route::group(['prefix'=>'website','namespace'=>'Website'], function(){
		Route::post('banner','BannerController@createOrUpdate');
		Route::get('list-banner','BannerController@list');
		Route::post('partner','PartnerController@createOrUpdate');
		Route::post('prize','PartnerController@createOrUpdatePrize');
		Route::get('list-partner','PartnerController@listPartner');
		Route::get('list-prize','PartnerController@listPrize');
		Route::get('setting','SettingController@setting');
		Route::post('save-setting','SettingController@postsetting');
		Route::post('founder','FounderController@createOrUpdate');
		Route::get('list-founder','FounderController@list');
		Route::post('video','VideoController@createOrUpdateVideo');
		Route::get('list-video','VideoController@listVideo');
		Route::post('albumaffter','AlbumAffterController@createOrUpdateAlbumAffter');
		Route::get('list-albumaffter','AlbumAffterController@listAlbumAfftero');
		Route::post('socicalfeedback','SocicalFeedbackController@createOrUpdateSocicalFeedback');
		Route::get('list-socicalfeedback','SocicalFeedbackController@listSocicalFeedback');
	});
	Route::group(['prefix'=>'customer','namespace'=>'Customer'], function(){
		Route::post('list','CustomerController@list');
		Route::post('add','CustomerController@create');
		Route::get('edit/{id}','CustomerController@getEdit');
		Route::post('active','CustomerController@activeCustomer');
		Route::post('resetPass','CustomerController@resetPass');
		Route::post('changeStatus','CustomerController@changeStatus');
		Route::post('edit-profile','CustomerController@postEdit');
		Route::get('delete/{id}','CustomerController@deleteCustomer');
		Route::get('result/{id}','CustomerController@resultDetail');
	});
	
	Route::group(['prefix'=>'home'], function(){
		Route::post('chart','HomeController@chart');
		Route::get('analytics',function(){
			$analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));
			$data = [];
			if(count($analyticsData) > 0 ){
				foreach($analyticsData as $item){
					$obj = new \stdClass;
					$obj->label = $item['date']->date;
					$obj->value = $item['pageViews'];
					$data[] = $obj;
				}
			}
			return response()->json([
				'data'=> $data,
				'message' => 'success'
			]);
		});
		Route::post('search_navbar','HomeController@searchNavbar');
	});
});
