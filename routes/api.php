<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\MasterSettingsController;
use App\Http\Controllers\PromotionalNoticeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PromotionalSiteController;
use App\Http\Controllers\WebsiteController;

Route::post('/auth/register', [AuthController::class, 'registerUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::get('country-list', [MasterSettingsController::class, 'countryList']);
Route::get('school-list', [SchoolController::class, 'schoolList']);
Route::get('get-expert-list', [AuthController::class, 'getExpertList']);

Route::post('client-info-save', [PromotionalSiteController::class, 'clientInfoSave']);
Route::get('client-list', [PromotionalSiteController::class, 'clientList']);

// Location
Route::get('division-list', [LocationController::class, 'divisionList']);
Route::get('district-list/{division_id}', [LocationController::class, 'districtListByID']);
Route::get('upazila-list/{district_id}', [LocationController::class, 'upazilaListByID']);
Route::get('area-list/{upazilla_id}', [LocationController::class, 'unionListByID']);


Route::get('menu-list', [MasterSettingsController::class, 'adminMenuList']);

//Tags
Route::get('tag-list', [MasterSettingsController::class, 'tagsList']);

Route::get('organization-list', [OrganizationController::class, 'organizationList']);
Route::get('settings-by-slug/{slug}', [MasterSettingsController::class, 'settingDetailsByID']);

Route::get('class-list', [ContentController::class, 'classList']);

Route::group(['prefix' => 'mobile'], function () {
    Route::get('menu-list', [MasterSettingsController::class, 'mobileMenuList']);
    Route::get('course-list-by-id/{menu_id}', [MasterSettingsController::class, 'courseListByID']);
    Route::get('all-course-list', [CourseController::class, 'allCourseList']);
    Route::get('all-content-list', [ContentController::class, 'allContentList']);
    Route::get('course-details-by-id/{course_id}', [CourseController::class, 'courseDetailsByID']);
    Route::get('all-mentor-list', [MentorController::class, 'allMentorList']);
    Route::get('mentor-details-by-id/{mentor_id}', [MentorController::class, 'mentorDetailsByID']);
    //Route::get('course-details-by-user/{course_id}', [CourseController::class, 'courseDetailsByUserID']);
});

Route::group(['prefix' => 'website'], function () {
    Route::get('menu-list', [MasterSettingsController::class, 'websiteMenuList']);
    Route::get('course-list-by-id/{menu_id}', [CourseController::class, 'courseListByID']);
    Route::get('course-details-by-user/{user_id}/{course_id}', [CourseController::class, 'courseDetailsByUserID']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('get-profile', [AuthController::class, 'getProfile']);
    Route::post('profile-update', [AuthController::class, 'updateUser']);
    Route::post('update-interest', [AuthController::class, 'updateInterest']);

    Route::group(['prefix' => 'mobile'], function () {
        Route::get('student-profile', [StudentController::class, 'studentDetails']);
    });

    Route::group(['prefix' => 'website'], function () {
        Route::post('purchase-course', [PaymentController::class, 'purchaseCourse']);
        Route::get('mentor-course-list', [MentorController::class, 'myCourseList']);
        Route::get('mentor-student-list', [CourseController::class, 'mentorStudentList']);
        Route::get('mentor-schedule-list/{mapping_id}', [CourseController::class, 'mentorClassScheduleList']);
        Route::get('mentor-completed-class-list', [CourseController::class, 'mentorCompletedClassList']);
    });
    
    //Menu Settings
    Route::get('admin/menu-list', [MasterSettingsController::class, 'adminMenuList']);
    Route::post('admin/menu-save-or-update', [MasterSettingsController::class, 'saveOrUpdateMenu']);

    //Organization
    Route::post('admin/organization-save-or-update', [OrganizationController::class, 'saveOrUpdateOrganization']);
    Route::post('admin/settings-update', [OrganizationController::class, 'updateSettings']);

    //Tags
    Route::post('admin/tag-save-or-update', [MasterSettingsController::class, 'saveOrUpdateTags']);

    //USER ROUTES
    Route::post('update-tags', [StudentController::class, 'updateInterests']);

    // common routes
    Route::get('admin/subject-by-class-id/{class_id}', [ContentController::class, 'subjectListByClassID']);
    Route::get('admin/chapter-by-subject-id/{subject_id}', [ContentController::class, 'chapterListBySubjectID']);
    Route::get('admin/script-list-by-chapter-id/{chapter_id}', [ContentController::class, 'scriptListByChapterID']);
    Route::get('admin/video-list-by-chapter-id/{chapter_id}', [ContentController::class, 'videoListByChapterID']);
    Route::get('admin/quiz-list-by-chapter-id/{chapter_id}', [ContentController::class, 'quizListByChapterID']);

    //admin Content Routes 
    Route::get('admin/class-list', [ContentController::class, 'classList']);
    Route::post('admin/class-save-or-update', [ContentController::class, 'saveOrUpdateClass']);
    Route::get('admin/subject-list', [ContentController::class, 'subjectList']);
    Route::post('admin/subject-save-or-update', [ContentController::class, 'saveOrUpdateSubject']);

    Route::get('admin/chapter-list', [ContentController::class, 'chapterList']);
    Route::post('admin/chapter-save-or-update', [ContentController::class, 'saveOrUpdateChapter']);
    Route::get('admin/video-chapter-list', [ContentController::class, 'videoChapterList']);
    Route::post('admin/chapter-video-save-or-update', [ContentController::class, 'saveOrUpdateChapterVideo']);

    Route::get('admin/chapter-script-list', [ContentController::class, 'scriptChapterList']);
    Route::post('admin/chapter-script-save-or-update', [ContentController::class, 'saveOrUpdateScript']);

    Route::post('admin/chapter-quiz-save-or-update', [ContentController::class, 'saveOrUpdateQuiz']);
    Route::get('admin/chapter-quiz-list', [ContentController::class, 'chapterQuizList']);

    Route::get('admin/question-set-list', [ContentController::class, 'questionSetList']);
    Route::get('admin/question-list-by-quiz/{id}', [ContentController::class, 'quizQuestionList']);
    Route::post('admin/chapter-quiz-question-save-or-update', [ContentController::class, 'saveOrUpdateQuizQuestion']);
    Route::post('admin/excel-question-upload', [ContentController::class, 'excelQuestionUpload']);
    Route::delete('admin/delete-question/{id}', [ContentController::class, 'deleteQuestion']);

    //admin Website 
    Route::post('admin/website-page-save-or-update', [MasterSettingsController::class, 'websitePageSaveOrUpdate']);
    Route::get('admin/website-page-list/{id}', [MasterSettingsController::class, 'websitePageList']);

    //admin Course 

    Route::get('admin/course-list', [CourseController::class, 'courseList']);
    Route::post('admin/course-save-or-update', [CourseController::class, 'saveOrUpdateCourse']);

    Route::post('admin/course-outline-save-or-update', [CourseController::class, 'saveOrUpdateCourseOutline']);
    Route::get('admin/course-outline-list/{id}', [CourseController::class, 'courseOutlineList']);

    Route::delete('admin/delete-course-outline/{id}', [CourseController::class, 'courseOutlineDelete']);
    Route::get('admin/content-list', [ContentController::class, 'contentList']);
    Route::post('admin/content-save-or-update', [ContentController::class, 'saveOrUpdateContent']);
    Route::post('admin/content-outline-save-or-update', [ContentController::class, 'saveOrUpdateContentOutline']);
    Route::get('admin/content-outline-list/{id}', [ContentController::class, 'contentOutlineList']);

    Route::delete('admin/delete-course-outline/{id}', [ContentController::class, 'courseOutlineDelete']);
    Route::delete('admin/delete-content-outline/{id}', [ContentController::class, 'contentOutlineDelete']);


    Route::post('admin/faq-save-or-update', [CourseController::class, 'saveOrUpdateFaq']);
    Route::get('admin/faq-list/{id}', [CourseController::class, 'faqList']);
    Route::delete('admin/delete-faq/{id}', [CourseController::class, 'faqDelete']);

    Route::post('admin/feature-save-or-update', [CourseController::class, 'saveOrUpdateFeature']);
    Route::get('admin/feature-list/{id}', [CourseController::class, 'featureList']);
    Route::delete('admin/delete-feature/{id}', [CourseController::class, 'featureDelete']);

    Route::post('admin/routine-save-or-update', [CourseController::class, 'saveOrUpdateRoutine']);
    Route::get('admin/routine-list/{id}', [CourseController::class, 'routineList']);
    Route::delete('admin/delete-routine/{id}', [CourseController::class, 'routineDelete']);

    Route::post('admin/mentor-assign-save-or-update', [CourseController::class, 'saveOrUpdateAssignMentor']);
    Route::get('admin/course-mentor-assign-list/{id}', [CourseController::class, 'mentorAssignList']);
    Route::get('admin/mentor-list', [CourseController::class, 'courseMentorList']);
    Route::delete('admin/delete-mentor-assign/{id}', [CourseController::class, 'mentorAssignDelete']);


    Route::post('admin/mentor-save-or-update', [MentorController::class, 'mentorSaveOrUpdate']);
    Route::get('admin/all-mentor-list-admin', [MentorController::class, 'allMentorListAdmin']);
    Route::post('admin/student-save-or-update', [StudentController::class, 'studentSaveOrUpdate']);



    
});

Route::group(['prefix' => 'open'], function () {
    // Package 
    Route::get('package-list', [PackageController::class, 'packageList']);
    Route::get('package-details-by-id/{package_id}', [PackageController::class, 'packageDetailsByID']);
    Route::get('syllabus-list', [MasterSettingsController::class, 'packageTypeList']);
});

Route::post('trancate-data', [MasterSettingsController::class, 'trancateData']);

Route::any('{url}', function () {
    return response()->json([
        'status' => false,
        'message' => 'Route Not Found!',
        'data' => []
    ], 404);
})->where('url', '.*');
