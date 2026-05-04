import Vue from 'vue';
import VueRouter from 'vue-router';

const _import = require('./_import_sync');
import store from '../store/index';
import CONSTANTS from '../core/utils/constants';
import ENUM from "../../config/enum";

Vue.use(VueRouter); 
let _routers = [
            {
                name:'login',
                path:'/login',
                component: _import('auth/login'),
                meta:{
                    requiresVisitor: true,
                }
            },
            {
                name:'changepass',
                path:'/changepass',
                component: _import('auth/changepass'),
                meta:{
                    requiresAuth: true,
                }
            },
            {
                name: 'list_quiz_category_main',
                path: '/quiz/categorymain',
                component: _import('quizCateMain/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_quiz_category_main',
                path: '/quiz/categorymain/add',
                component: _import('quizCateMain/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_quiz_category_main',
                path: '/quiz/categorymain/edit/:id',
                component: _import('quizCateMain/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_quiz_category',
                path: '/quiz/category',
                component: _import('quizCate/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_quiz_category',
                path: '/quiz/category/add',
                component: _import('quizCate/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_quiz_category',
                path: '/quiz/category/edit/:id',
                component: _import('quizCate/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_quiz_exam',
                path: '/quiz/exam',
                component: _import('quizExam/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_quiz_exam',
                path: '/quiz/exam/add',
                component: _import('quizExam/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_quiz_exam',
                path: '/quiz/exam/edit/:id',
                component: _import('quizExam/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            
            {
                name: 'list_quiz_part',
                path: '/quiz/part',
                component: _import('quizPart/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_quiz_part',
                path: '/quiz/part/add',
                component: _import('quizPart/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_quiz_part',
                path: '/quiz/part/edit/:id',
                component: _import('quizPart/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'questionImport',
                path: '/quiz/question/import',
                component: _import('quizQuestion/importexcel'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_quiz_question',
                path: '/quiz/question',
                component: _import('quizQuestion/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_quiz_question',
                path: '/quiz/question/add',
                component: _import('quizQuestion/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_quiz_question',
                path: '/quiz/question/edit/:id',
                component: _import('quizQuestion/edit'),
                meta: {
                    requiresAuth: true,
                }
            },

            {
                name: 'list_quiz_question_group',
                path: '/quiz/question_group',
                component: _import('quizQuestionGroup/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_quiz_question_group',
                path: '/quiz/question_group/add',
                component: _import('quizQuestionGroup/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_quiz_question_group',
                path: '/quiz/question_group/edit/:id',
                component: _import('quizQuestionGroup/edit'),
                meta: {
                    requiresAuth: true,
                }
            },



            {
                name: 'list_docs_category_main',
                path: '/docs/categorymain',
                component: _import('docsCateMain/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_docs_category_main',
                path: '/docs/categorymain/add',
                component: _import('docsCateMain/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_docs_category_main',
                path: '/docs/categorymain/edit/:id',
                component: _import('docsCateMain/edit'),
                meta: {
                    requiresAuth: true,
                }
            },

            {
                name: 'list_docs_category',
                path: '/docs/category',
                component: _import('docsCate/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_docs_category',
                path: '/docs/category/add',
                component: _import('docsCate/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_docs_category',
                path: '/docs/category/edit/:id',
                component: _import('docsCate/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_docs_exam',
                path: '/docs/exam',
                component: _import('docsExam/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_docs_exam',
                path: '/docs/exam/add',
                component: _import('docsExam/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_docs_exam',
                path: '/docs/exam/edit/:id',
                component: _import('docsExam/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            
            {
                name: 'list_docs_part',
                path: '/docs/part',
                component: _import('docsPart/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_docs_part',
                path: '/docs/part/add',
                component: _import('docsPart/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_docs_part',
                path: '/docs/part/edit/:id',
                component: _import('docsPart/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'questionImportDocs',
                path: '/docs/question/import',
                component: _import('docsQuestion/importexcel'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_docs_question',
                path: '/docs/question',
                component: _import('docsQuestion/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_docs_question',
                path: '/docs/question/add',
                component: _import('docsQuestion/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_docs_question',
                path: '/docs/question/edit/:id',
                component: _import('docsQuestion/edit'),
                meta: {
                    requiresAuth: true,
                }
            },

            {
                name: 'list_docs_question_group',
                path: '/docs/question_group',
                component: _import('docsQuestionGroup/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_docs_question_group',
                path: '/docs/question_group/add',
                component: _import('docsQuestionGroup/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_docs_question_group',
                path: '/docs/question_group/edit/:id',
                component: _import('docsQuestionGroup/edit'),
                meta: {
                    requiresAuth: true,
                }
            },




            {
                name: 'register',
                path: '/register',
                component: _import('auth/register'),
                meta: {
                    requiresVisitor: true,
                }
            },
            {
                name: 'home',
                path: '/',
                component: _import('products/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_category',
                path: '/product/category',
                component: _import('cate/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_category',
                path: '/product/category/add',
                component: _import('cate/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_category',
                path: '/product/category/edit/:id',
                component: _import('cate/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_type_cate',
                path: '/product/type',
                component: _import('typeProduct/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_type_cate',
                path: '/product/type/edit/:quiz_cate/:language',
                component: _import('typeProduct/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_type_cate',
                path: '/product/type/add',
                component: _import('typeProduct/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'listProduct',
                path: '/product',
                component: _import('products/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_product',
                path: '/product/edit/:id',
                component: _import('products/test'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'createProduct',
                path: '/product/create',
                component: _import('products/add'),
                meta: {
                    requiresAuth: true,
                }
            },
             {
                name: 'listTest',
                path: '/product/list/test',
                component: _import('products/baitaplist'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edittest',
                path: '/product/test/edit/:id',
                component: _import('products/baitapedit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'createTest',
                path: '/product/baitap',
                component: _import('products/baitap'),
                meta: {
                    requiresAuth: true,
                }
            },
            
            {
                name: 'listDocumentCate',
                path: '/tailieu/cate/list',
                component: _import('tailieu/listcate'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editDocumentCate',
                path: '/tailieu/cate/edit/:id',
                component: _import('tailieu/editcate'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'createDocumentCate',
                path: '/tailieu/cate/add',
                component: _import('tailieu/addcate'),
                meta: {
                    requiresAuth: true,
                }
            },


            {
                name: 'listBlogs',
                path: '/blogs',
                component: _import('blogs/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'addBlogs',
                path: '/blog/add',
                component: _import('blogs/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editBlog',
                path: '/blog/edit/:id',
                component: _import('blogs/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'listCateBlog',
                path: '/blog/category',
                component: _import('blogs/category/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editCateBlog',
                path: '/blog/category/edit/:id',
                component: _import('blogs/category/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editTypeBlog',
                path: '/blog/type/edit/:id',
                component: _import('blogs/type/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'listTypeBlog',
                path: '/blog/type',
                component: _import('blogs/type/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'language',
                path: '/language',
                component: _import('language/language'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'languageKeyword',
                path: '/language/keyword',
                component: _import('language/keyword'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'pageContent',
                path: '/pagecontent',
                component: _import('pagecontent/list'),
                meta: {
                    requiresAuth: true,
                }
            },
             {
                name: 'pageContentAdd',
                path: '/pagecontent/add',
                component: _import('pagecontent/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'pageContentEdit',
                path: '/pagecontent/edit/:quiz_id/:language',
                component: _import('pagecontent/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'banner',
                path: '/banner',
                component: _import('website/banner'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'founder',
                path: '/founder',
                component: _import('website/founder'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'partner',
                path: '/partner',
                component: _import('website/partner'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'prize',
                path: '/prize',
                component: _import('website/prize'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'video',
                path: '/video',
                component: _import('website/video'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'albumAffter',
                path: '/albumAffter',
                component: _import('website/albumAffter'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'socicalFeedback',
                path: '/socicalFeedback',
                component: _import('website/socialNetwork'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'setting',
                path: '/setting',
                component: _import('website/setting'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'entrance',
                path: '/entrance',
                component: _import('entrance/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'entranceEdit',
                path: '/entrance/edit/:id',
                component: _import('entrance/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'customer',
                path: '/customer',
                component: _import('customer/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'customerAdd',
                path: '/customer/add',
                component: _import('customer/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'customerEdit',
                path: '/customer/edit/:id_customer',
                component: _import('customer/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'detailResult',
                path: '/customer/result/:id_result',
                component: _import('customer/detail_result'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billCoursePaymented',
                path: '/bill/course/paymented',
                component: _import('billCourse/paymented'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billCourseDraft',
                path: '/bill/course/draft',
                component: _import('billCourse/draft'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billCourserDetail',
                path: '/bill/course/detail/:code_bill',
                component: _import('billCourse/detail'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billDethiPaymented',
                path: '/bill/dethi/paymented',
                component: _import('billDethi/paymented'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billDethiDraft',
                path: '/bill/dethi/draft',
                component: _import('billDethi/draft'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billDethiDetail',
                path: '/bill/dethi/detail/:bill_id',
                component: _import('billDethi/detail'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billAdd',
                path: '/bill/add',
                component: _import('bill/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billDetail',
                path: '/bill/detail/:code_bill',
                component: _import('bill/detail'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billDraft',
                path: '/bill/draft',
                component: _import('bill/draft'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billPaymented',
                path: '/bill/paymented',
                component: _import('bill/paymented'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billUnPayment',
                path: '/bill/Unpayment',
                component: _import('bill/unpayment'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'listPromotion',
                path: '/promotion',
                component: _import('promotion/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'addPromotion',
                path: '/promotion/add',
                component: _import('promotion/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editPromotion',
                path: '/promotion/edit/:id',
                component: _import('promotion/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'listMessContact',
                path: '/messcontact',
                component: _import('messcontact/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'listService',
                path: '/service',
                component: _import('service/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'addService',
                path: '/service/add',
                component: _import('service/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editService',
                path: '/service/edit/:id',
                component: _import('service/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'listReviewCus',
                path: '/reviewCus',
                component: _import('reviewcus/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'addReviewCus',
                path: '/reviewCus/add',
                component: _import('reviewcus/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editReviewCus',
                path: '/reviewCus/edit/:id',
                component: _import('reviewcus/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_type_two',
                path: '/product/typetwo',
                component: _import('typetwo/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_type_two',
                path: '/product/typetwo/add',
                component: _import('typetwo/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_type_two',
                path: '/product/typetwo/edit/:id',
                component: _import('typetwo/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            
            {
                name: 'listBannerads',
                path: '/bannerads',
                component: _import('bannerads/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'addBannerads',
                path: '/bannerads/add',
                component: _import('bannerads/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editBannerads',
                path: '/bannerads/edit/:id',
                component: _import('bannerads/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_category_service',
                path: '/service/category',
                component: _import('serviceCate/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_category_service',
                path: '/service/category/add',
                component: _import('serviceCate/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_category_service',
                path: '/service/category/edit/:id',
                component: _import('serviceCate/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_solution',
                path: '/solution',
                component: _import('solution/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_solution',
                path: '/solution/add',
                component: _import('solution/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_solution',
                path: '/solution/edit/:id',
                component: _import('solution/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'list_project',
                path: '/project',
                component: _import('project/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_project',
                path: '/project/add',
                component: _import('project/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_project',
                path: '/project/edit/:id',
                component: _import('project/edit'),
                meta: {
                    requiresAuth: true,
                }
            },


            {
                name: 'listDocument',
                path: '/document',
                component: _import('document/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'addDocument',
                path: '/document/add',
                component: _import('document/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editDocument',
                path: '/document/edit/:id',
                component: _import('document/edit'),
                meta: {
                    requiresAuth: true,
                }
            },

            {
                name: 'list_category_document',
                path: '/document/category',
                component: _import('documentCate/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'add_category_document',
                path: '/document/category/add',
                component: _import('documentCate/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'edit_category_document',
                path: '/document/category/edit/:id',
                component: _import('documentCate/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billDocumentPaymented',
                path: '/bill/document/paymented',
                component: _import('billDocument/paymented'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billDocumentDraft',
                path: '/bill/document/draft',
                component: _import('billDocument/draft'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'billDocumentDetail',
                path: '/bill/document/detail/:code_bill',
                component: _import('billDocument/detail'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'listDocument',
                path: '/tailieu/list',
                component: _import('tailieu/list'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'editDocument',
                path: '/tailieu/edit/:id',
                component: _import('tailieu/edit'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'createDocument',
                path: '/tailieu/add',
                component: _import('tailieu/add'),
                meta: {
                    requiresAuth: true,
                }
            },
            {
                name: 'listDethi',
                path: '/de-thi/list',
                component: _import('dethi/list'),
                meta: {
                    requiresAuth: true,
                }
            },

];
const router = new VueRouter({
    errorHandler(to, from, next, error) {
    },
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return {x: 0, y: 0}
        }
    },
    routes: _routers
});
router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!store.getters.isLoggedIn) {
            next({
                name: 'login'
            })
        } else {
            next()
        }
    } else if (to.matched.some(record => record.meta.requiresVisitor)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (store.getters.isLoggedIn) {
            next({
                name: 'home'
            })
        } else {
            next()
        }
    } else {
        next() // make sure to always call next()!
    }
})

export default router;
