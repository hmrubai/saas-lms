<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\MasterSettingsController;
use App\Http\Controllers\PromotionalNoticeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ConsumeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CorrectionController;


Route::post('/auth/register', [AuthController::class, 'registerUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::get('country-list', [MasterSettingsController::class, 'countryList']);
Route::get('school-list', [SchoolController::class, 'schoolList']);
Route::get('get-expert-list', [AuthController::class, 'getExpertList']);

Route::middleware('auth:sanctum')->group( function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('get-profile', [AuthController::class, 'getProfile']);

    Route::post('profile-update', [AuthController::class, 'updateUser']);

    //Master Settings
    Route::get('syllabus-list', [MasterSettingsController::class, 'packageTypeList']);
    Route::get('grade-list', [MasterSettingsController::class, 'gradeList']);
    Route::get('category-list', [MasterSettingsController::class, 'categoryList']);

    //Package 
    Route::get('package-list', [PackageController::class, 'packageList']);
    Route::get('package-details-by-id/{package_id}', [PackageController::class, 'packageDetailsByID']);

    //Topic
    Route::get('all-topic-list', [TopicController::class, 'allTopicList']);
    Route::post('filter-topic-list', [TopicController::class, 'fillterTopicList']);
    Route::get('filter-topic-list/{syllabus_id}', [TopicController::class, 'fillterTopicListByTypeID']);

    //Package Details (For User)
    Route::get('my-package-list', [ConsumeController::class, 'myPackageList']);
    Route::get('my-active-syllebus-list/{payment_id}', [ConsumeController::class, 'myActiveSyllebusList']);
    
    //Promotional Notice
    Route::get('promotional-news-list', [PromotionalNoticeController::class, 'promotionalNoticeList']);

    //Admin
    Route::get('admin/syllabus-list', [MasterSettingsController::class, 'admin_PackageTypeList']);
    Route::post('admin/syllabus-save-or-update', [MasterSettingsController::class, 'saveOrUpdatePackageType']);
    Route::get('admin/grade-list', [MasterSettingsController::class, 'adminGradeList']);
    Route::post('admin/grade-save-or-update', [MasterSettingsController::class, 'saveOrUpdateGrade']);
    Route::get('admin/category-list', [MasterSettingsController::class, 'adminCategoryList']);
    Route::post('admin/category-save-or-update', [MasterSettingsController::class, 'saveOrUpdateCategory']);

    Route::get('admin/package-list', [PackageController::class, 'adminPackageList']);
    Route::post('admin/package-save-or-update', [PackageController::class, 'saveOrUpdatePackage']);
    Route::get('admin/benefit-list-by-id/{package_id}', [PackageController::class, 'adminBenefitListByID']);
    Route::post('admin/benefit-save-or-update', [PackageController::class, 'saveOrUpdateBenefit']);
    Route::post('admin/benefit-delete', [PackageController::class, 'adminDeleteBenefitByID']);

    Route::get('admin/news-list', [PromotionalNoticeController::class, 'adminPromotionalNoticeList']);
    Route::post('admin/news-save-or-update', [PromotionalNoticeController::class, 'saveOrUpdatePromotionalNotice']);

    Route::get('admin/topic-list', [TopicController::class, 'adminTopicList']);
    Route::post('admin/topic-save-or-update', [TopicController::class, 'saveOrUpdateTopic']);

    Route::get('admin/school-list', [SchoolController::class, 'adminSchoolList']);
    Route::post('admin/school-save-or-update', [SchoolController::class, 'saveOrUpdateSchool']);
    Route::get('admin/expert-list', [AuthController::class, 'getAdminExpertList']);
    Route::post('admin/save-update-expert', [AuthController::class, 'saveOrUpdateUser']);
    Route::get('admin/payment-list', [PaymentController::class, 'adminPaymentList']);
    Route::post('delete-account', [AuthController::class, 'deleteUserAccount']);
    
    //Payment 
    Route::post('mobile/make-payment', [PaymentController::class, 'makePaymentMobile']);
    Route::get('payment-list', [PaymentController::class, 'myPaymentList']);
    Route::get('package-details-by-payment-id/{payment_id}', [PaymentController::class, 'packageDetailsByPaymentID']);

    //Payment Web
    Route::post('web/make-payment', [PaymentController::class, 'makePaymentWeb']);

    //Submit Correction 
    Route::post('check-availability', [CorrectionController::class, 'checkAvailable']);
    Route::post('submit-correction', [CorrectionController::class, 'submitCorrection']);
    Route::post('edit-correction-by-student', [CorrectionController::class, 'editCorrectionByStudent']);
    Route::post('update-is-seen-by-student', [CorrectionController::class, 'updateIsSeenByStudent']);
    Route::get('pending-correction-count', [CorrectionController::class, 'getPendingCorrectionCount']);
    Route::get('correction-list', [CorrectionController::class, 'getCorrectionList']);
    Route::get('correction-details-by-id/{correction_id}', [CorrectionController::class, 'getCorrectionDetailsByID']);
    Route::get('expert-correction-list', [CorrectionController::class, 'getExpertCorrectionList']);
    Route::post('accept-correction', [CorrectionController::class, 'acceptPendingCorrection']);
    Route::post('submit-feedback', [CorrectionController::class, 'submitFeedback']);
    Route::get('mark-grade-list', [MasterSettingsController::class, 'markGradeList']);
    Route::post('update-feedback', [CorrectionController::class, 'editFeedback']);
    Route::post('student-resubmission', [CorrectionController::class, 'studentResubmission']);
    Route::post('submit-final-note', [CorrectionController::class, 'submitExpertFinalNote']);
    Route::post('submit-rating-by-student', [CorrectionController::class, 'submitStudentRating']);
    Route::get('expert-dashboard', [CorrectionController::class, 'getMiniDashboardInfo']);

    Route::get('my-balance-list', [ConsumeController::class, 'myBalanceList']); 
});

Route::group(['prefix' => 'open'], function(){
    // Package 
    Route::get('package-list', [PackageController::class, 'packageList']);
    Route::get('package-details-by-id/{package_id}', [PackageController::class, 'packageDetailsByID']);
    Route::get('syllabus-list', [MasterSettingsController::class, 'packageTypeList']);
});

Route::post('trancate-data', [MasterSettingsController::class, 'trancateData']);

Route::any('{url}', function(){
    return response()->json([
        'status' => false,
        'message' => 'Route Not Found!',
        'data' => []
    ], 404);
})->where('url', '.*');
