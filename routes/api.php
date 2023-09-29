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
    Route::get('quiz-details-by-id/{quiz_id}', [CourseController::class, 'chapterQuizDetails']);

    Route::get('organization-details-by-id/{organization_id}', [OrganizationController::class, 'organizationDetailsByID']);

    Route::get('content-details-by-id/{content_id}', [ContentController::class, 'contentDetailsByID']);
    Route::get('content-outline-details-by-id/{content_subject_id}', [ContentController::class, 'ContentOutlineDetailsByID']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('get-profile', [AuthController::class, 'getProfile']);
    Route::post('profile-update', [AuthController::class, 'updateUser']);
    Route::post('update-interest', [AuthController::class, 'updateInterest']);
    Route::post('update-tags', [StudentController::class, 'updateInterests']);

    Route::group(['prefix' => 'mobile'], function () {
        Route::get('student-profile', [StudentController::class, 'studentDetails']);
    });

    Route::group(['prefix' => 'website'], function () {
        Route::post('purchase-course', [PaymentController::class, 'purchaseCourse']);
        Route::get('mentor-course-list', [MentorController::class, 'myCourseList']);
        Route::get('mentor-student-list', [CourseController::class, 'mentorStudentList']);
        Route::get('mentor-student-list-by-course/{course_id}', [CourseController::class, 'mentorStudentListByCourse']);
        Route::get('mentor-schedule-list/{mapping_id}', [CourseController::class, 'mentorClassScheduleList']);
        Route::get('mentor-completed-class-list', [CourseController::class, 'mentorCompletedClassList']);
        Route::get('mentor-ongoing-class-list', [CourseController::class, 'mentorOngoingClassList']);
        Route::post('add-new-schedule', [CourseController::class, 'addClassSchedule']);
        Route::post('update-schedule', [CourseController::class, 'updateClassSchedule']);
        Route::post('delete-schedule', [CourseController::class, 'deleteClassSchedule']);
        Route::post('start-live-class', [CourseController::class, 'startLiveClass']);
        Route::post('end-live-class', [CourseController::class, 'endLiveClass']);

        Route::post('join-live-class', [CourseController::class, 'studentJoinClass']);
        Route::get('student-join-history/{schedule_id}', [CourseController::class, 'studentClassJoinHistory']);

        Route::get('student-details-by-mapping-id/{mapping_id}', [StudentController::class, 'studentDetailsByMappingID']);

        Route::get('mentor-live-link', [MentorController::class, 'getZoomLink']);
        Route::post('update-link', [MentorController::class, 'updateZoomLink']);

        Route::get('student-course-list', [StudentController::class, 'myCourseList']);
        Route::get('student-class-list', [CourseController::class, 'studentClassList']);
        Route::get('student-purchase-list', [StudentController::class, 'myPurchaseList']);

        Route::post('start-quiz', [CourseController::class, 'startQuiz']);
        Route::post('submit-quiz', [CourseController::class, 'submitQuizAnswer']);
        Route::post('submit-written-answer', [CourseController::class, 'submitWrittenAnswer']);
        Route::get('student-quiz-participated-list', [CourseController::class, 'quizAnswerList']);
        Route::get('student-quiz-result-details-by-id/{result_id}', [CourseController::class, 'quizAnswerDetails']);
        Route::get('student-subject-wise-result/{result_id}', [CourseController::class, 'quizSubjectWiseAnswerDetails']);

        //Assignment Routes 
        Route::get('class-list', [ContentController::class, 'classList']);
        Route::get('subject-list-by-class-id/{class_id}', [ContentController::class, 'subjectListByClassID']);
        Route::get('chapter-list-by-subject-id/{subject_id}', [ContentController::class, 'chapterListBySubjectID']);
        
        Route::get('resource-list-by-chapter-id/{chapter_id}', [ContentController::class, 'resourceListByChapterID']);

    });

    //All Admin Routes Start
    Route::group(['prefix' => 'admin'], function () {
        //Menu Settings
        Route::get('menu-list', [MasterSettingsController::class, 'adminMenuList']);
        Route::post('menu-save-or-update', [MasterSettingsController::class, 'saveOrUpdateMenu']);

        //Organization
        Route::get('organization-list', [OrganizationController::class, 'organizationListAdmin']);
        Route::post('organization-save-or-update', [OrganizationController::class, 'saveOrUpdateOrganization']);
        Route::post('settings-update', [OrganizationController::class, 'updateSettings']);

        //Tags
        Route::post('tag-save-or-update', [MasterSettingsController::class, 'saveOrUpdateTags']);
        Route::post('tag-save-or-update-admin', [MasterSettingsController::class, 'tagsSaveOrUpdateAdmin']);

        Route::delete('delete-tag/{id}', [MasterSettingsController::class, 'tagsDelete']);
        Route::get('tag-list-admin', [MasterSettingsController::class, 'tagsListAdmin']);

        // common routes
        Route::get('subject-by-class-id/{class_id}', [ContentController::class, 'subjectListByClassID']);
        Route::get('chapter-by-subject-id/{subject_id}', [ContentController::class, 'chapterListBySubjectID']);
        Route::get('script-list-by-chapter-id/{chapter_id}', [ContentController::class, 'scriptListByChapterID']);
        Route::get('video-list-by-chapter-id/{chapter_id}', [ContentController::class, 'videoListByChapterID']);
        Route::get('quiz-list-by-chapter-id/{chapter_id}', [ContentController::class, 'quizListByChapterID']);
        Route::get('quiz-details-by-id/{id}', [ContentController::class, 'quizDetailsById']);

        //Content Routes 
        Route::get('class-list', [ContentController::class, 'classList']);
        Route::post('class-save-or-update', [ContentController::class, 'saveOrUpdateClass']);
        Route::get('subject-list', [ContentController::class, 'subjectList']);
        Route::post('subject-save-or-update', [ContentController::class, 'saveOrUpdateSubject']);

        Route::get('chapter-list', [ContentController::class, 'chapterList']);
        Route::post('chapter-save-or-update', [ContentController::class, 'saveOrUpdateChapter']);
        Route::get('video-chapter-list', [ContentController::class, 'videoChapterList']);
        Route::post('chapter-video-save-or-update', [ContentController::class, 'saveOrUpdateChapterVideo']);

        Route::get('chapter-script-list', [ContentController::class, 'scriptChapterList']);
        Route::post('chapter-script-save-or-update', [ContentController::class, 'saveOrUpdateScript']);

        Route::post('chapter-quiz-save-or-update', [ContentController::class, 'saveOrUpdateQuiz']);
        Route::get('chapter-quiz-list', [ContentController::class, 'chapterQuizList']);
        Route::get('quiz-type-list', [ContentController::class, 'quizTypeList']);

        Route::get('core-subject-list', [ContentController::class, 'coreSubjectList']);
        Route::get('chapter-quiz-subject-list/{id}', [ContentController::class, 'chapterQuizSubjectList']);
        Route::post('quiz-subject-save-or-update',[ContentController::class, 'quizSubjectSaveOrUpdate']);
        Route::get('quiz-assign-subject-list/{id}', [ContentController::class, 'chapterQuizSubjectList']);

        Route::get('content-subject-list', [ContentController::class, 'contentSubjectList']);
        Route::post('content-subject-assign-save-or-update', [ContentController::class, 'contentSubjectAssignSaveOrUpdate']);

        //WrittenQuestion
        Route::get('written-question-list/{id}', [ContentController::class, 'writtenQuestionList']);
        Route::post('written-question-save-or-update', [ContentController::class, 'saveOrUpdateWrittenQuestion']);

        Route::get('question-set-list', [ContentController::class, 'questionSetList']);
        Route::get('question-list-by-quiz/{id}', [ContentController::class, 'quizQuestionList']);
        Route::post('chapter-quiz-question-save-or-update', [ContentController::class, 'saveOrUpdateQuizQuestion']);
        Route::post('excel-question-upload', [ContentController::class, 'excelQuestionUpload']);
        Route::post('delete-question', [ContentController::class, 'deleteQuestion']);

        //Website 
        Route::post('website-page-save-or-update', [MasterSettingsController::class, 'websitePageSaveOrUpdate']);
        Route::get('website-page-list/{id}', [MasterSettingsController::class, 'websitePageList']);

        //Course 
        Route::get('course-list', [CourseController::class, 'courseList']);
        Route::post('course-save-or-update', [CourseController::class, 'saveOrUpdateCourse']);
        Route::get('course-type',[CourseController::class, 'courseTypeList']);

        Route::post('course-outline-save-or-update', [CourseController::class, 'saveOrUpdateCourseOutline']);
        Route::get('course-outline-list/{id}', [CourseController::class, 'courseOutlineList']);
        Route::delete('delete-course-outline/{id}', [CourseController::class, 'courseOutlineDelete']);

        Route::get('content-list', [ContentController::class, 'contentList']);
        Route::post('content-save-or-update', [ContentController::class, 'saveOrUpdateContent']);
        Route::post('content-outline-save-or-update', [ContentController::class, 'saveOrUpdateContentOutline']);
        Route::get('content-outline-list/{id}', [ContentController::class, 'contentOutlineList']);
        Route::delete('delete-content-outline/{id}', [ContentController::class, 'contentOutlineDelete']);

        Route::post('faq-save-or-update', [CourseController::class, 'saveOrUpdateFaq']);
        Route::get('faq-list/{id}', [CourseController::class, 'faqList']);
        Route::delete('delete-faq/{id}', [CourseController::class, 'faqDelete']);

        Route::post('feature-save-or-update', [CourseController::class, 'saveOrUpdateFeature']);
        Route::get('feature-list/{id}', [CourseController::class, 'featureList']);
        Route::delete('delete-feature/{id}', [CourseController::class, 'featureDelete']);

        Route::post('routine-save-or-update', [CourseController::class, 'saveOrUpdateRoutine']);
        Route::get('routine-list/{id}', [CourseController::class, 'routineList']);
        Route::delete('delete-routine/{id}', [CourseController::class, 'routineDelete']);

        Route::post('mentor-assign-save-or-update', [CourseController::class, 'saveOrUpdateAssignMentor']);
        Route::get('course-mentor-assign-list/{id}', [CourseController::class, 'mentorAssignList']);
        Route::get('mentor-list', [CourseController::class, 'courseMentorList']);
        Route::get('student-list', [CourseController::class, 'courseStudentList']);
        Route::delete('delete-mentor-assign/{id}', [CourseController::class, 'mentorAssignDelete']);


        Route::post('mentor-save-or-update', [MentorController::class, 'mentorSaveOrUpdate']);
        Route::get('all-mentor-list-admin', [MentorController::class, 'allMentorListAdmin']);
        Route::post('student-save-or-update', [StudentController::class, 'studentSaveOrUpdate']);
        Route::get('all-student-list-admin', [StudentController::class, 'allStudentAdmin']);

        Route::post('student-mapping-save-or-update', [CourseController::class, 'saveOrUpdateStudentMapping']);
        Route::delete('course-student-mapping-delete/{id}', [CourseController::class, 'courseStudentMappingDelete']);
        Route::get('student-mapping-list', [CourseController::class, 'studentMappingList']);
        Route::get('course-list-for-mapping', [CourseController::class, 'courseListForStudentMapping']);
        Route::get('mentor-list-by-course', [CourseController::class, 'mentorListByCourse']);
        Route::get('student-Participant-list-by-course-id/{course_id}', [StudentController::class, 'courseParticipantList']);
        Route::get('course-payment-list-by-course-id/{course_id}', [StudentController::class, 'courseParticipantPaymentList']);

        Route::get('enrollment-list/{id}', [CourseController::class, 'enrollMentorList']);
        Route::post('course-free-enrollment', [CourseController::class, 'courseFreeEnrollment']);

    });
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
