<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use Auth;
use Exception;
use App\Models\User;
use App\Models\Grade;
use App\Models\Course;
use App\Models\Content;
use App\Models\ContentOutline;
use App\Models\CourseOutline;
use App\Models\CourseClassRoutine;
use App\Models\CourseFeature;
use App\Models\CourseMentor;
use App\Models\CourseFaq;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Country;
use App\Models\Interest;
use App\Models\Category;
use App\Models\PackageType;
use App\Models\Correction;
use App\Models\Organization;
use App\Models\CorrectionRating;
use App\Models\PaymentDetail;
use App\Models\TopicConsume;
use App\Models\WebsitePage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class MasterSettingsController extends Controller
{
    use HelperTrait;
    public function settingDetailsByID(Request $request)
    {
        $slug = $request->slug ? $request->slug : 0;

        $setting = Setting::where('organization_slug', $slug)->first();

        return response()->json([
            'status' => true,
            'message' => 'Settings Details',
            'data' => $setting
        ], 200);
    }

    public function trancateData(Request $request)
    {
        Correction::truncate();
        CorrectionRating::truncate();
        Payment::truncate();
        PaymentDetail::truncate();
        TopicConsume::truncate();

        return response()->json([
            'status' => true,
            'message' => 'Truncated Successful',
            'data' => []
        ], 200);
    }

    public function packageTypeList(Request $request)
    {
        $package_list = PackageType::select('id', 'name', 'price', 'limit')->where('is_active', true)->get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $package_list
        ], 200);
    }

    public function gradeList(Request $request)
    {
        $grade_list = Grade::select('id', 'name')->where('is_active', true)->get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $grade_list
        ], 200);
    }

    public function categoryList(Request $request)
    {
        $category_list = Category::select('id', 'name')->where('is_active', true)->get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $category_list
        ], 200);
    }

    public function countryList(Request $request)
    {
        $country_list = Country::select('id', 'country_name')->get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $country_list
        ], 200);
    }

    //Admin Methods
    public function admin_PackageTypeList(Request $request)
    {
        $package_list = PackageType::all();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $package_list
        ], 200);
    }

    public function saveOrUpdatePackageType(Request $request)
    {
        try {
            if ($request->id) {
                $type = PackageType::where('id', $request->id)->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Type has been updated successfully',
                    'data' => []
                ], 200);
            } else {
                $isExist = PackageType::where('name', $request->name)->first();
                if (empty($isExist)) {
                    $type = PackageType::create($request->all());
                    return response()->json([
                        'status' => true,
                        'message' => 'Type has been created successfully',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Type already Exist!',
                        'data' => []
                    ], 200);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 200);
        }
    }

    public function adminGradeList(Request $request)
    {
        $grade_list = Grade::all();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $grade_list
        ], 200);
    }

    public function saveOrUpdateGrade(Request $request)
    {
        try {
            if ($request->id) {
                $type = Grade::where('id', $request->id)->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Grade has been updated successfully',
                    'data' => []
                ], 200);
            } else {
                $isExist = Grade::where('name', $request->name)->first();
                if (empty($isExist)) {
                    $type = Grade::create($request->all());
                    return response()->json([
                        'status' => true,
                        'message' => 'Grade has been created successfully',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Grade already Exist!',
                        'data' => []
                    ], 200);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 200);
        }
    }

    public function adminMenuList(Request $request)
    {
        $menus = Category::all();
        foreach ($menus as $item) {

            if ($item->is_course) {
                $courses = Course::where('category_id', $item->id)->orderBy('sequence', 'ASC')->get();
                $item->courses = $courses;

                foreach ($courses as $course) {
                    $course->course_outline = CourseOutline::select(
                        'course_outlines.*',
                        'class_levels.name as class_name',
                        'subjects.name as subject_name',
                        'chapters.name as chapter_name'
                    )
                        ->where('course_outlines.course_id', $course->id)
                        ->leftJoin('class_levels', 'class_levels.id', 'course_outlines.class_level_id')
                        ->leftJoin('subjects', 'subjects.id', 'course_outlines.subject_id')
                        ->leftJoin('chapters', 'chapters.id', 'course_outlines.chapter_id')
                        ->get();

                    $course->course_routine = CourseClassRoutine::where('course_id', $course->id)->get();
                    $course->course_feature = CourseFeature::where('course_id', $course->id)->get();
                    $course->course_mentor = CourseMentor::where('course_id', $course->id)->get();
                    $course->course_faq = CourseFaq::where('course_id', $course->id)->get();
                }
            }

            if ($item->is_content) {
                $content_list = Content::where('category_id', $item->id)->get();
                $item->contents = $content_list;

                foreach ($content_list as $content) {
                    $content->content_outline = ContentOutline::select(
                        'content_outlines.*',
                        'class_levels.name as class_name',
                        'subjects.name as subject_name',
                        'chapters.name as chapter_name'
                    )
                        ->where('content_outlines.content_id', $content->id)
                        ->leftJoin('class_levels', 'class_levels.id', 'content_outlines.class_level_id')
                        ->leftJoin('subjects', 'subjects.id', 'content_outlines.subject_id')
                        ->leftJoin('chapters', 'chapters.id', 'content_outlines.chapter_id')
                        ->get();
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $menus
        ], 200);
    }

    public function courseListByID(Request $request)
    {
        $menu_id = $request->menu_id ? $request->menu_id : 0;

        if (!$menu_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please, attach menu ID',
                'data' => []
            ], 422);
        }

        $menu = Category::where('id', $menu_id)->first();

        if (empty($menu)) {
            return response()->json([
                'status' => false,
                'message' => 'Menu not found!',
                'data' => []
            ], 404);
        }

        if ($menu->is_course) {
            $courses = Course::where('category_id', $menu->id)->orderBy('sequence', 'ASC')->get();
            $menu->courses = $courses;

            foreach ($courses as $course) {
                $course->course_outline = CourseOutline::select(
                    'course_outlines.*',
                    'class_levels.name as class_name',
                    'subjects.name as subject_name',
                    'chapters.name as chapter_name'
                )
                    ->where('course_outlines.course_id', $course->id)
                    ->leftJoin('class_levels', 'class_levels.id', 'course_outlines.class_level_id')
                    ->leftJoin('subjects', 'subjects.id', 'course_outlines.subject_id')
                    ->leftJoin('chapters', 'chapters.id', 'course_outlines.chapter_id')
                    ->get();

                $course->course_routine = CourseClassRoutine::where('course_id', $course->id)->get();
                $course->course_feature = CourseFeature::where('course_id', $course->id)->get();
                $course->course_mentor = CourseMentor::where('course_id', $course->id)->get();
                $course->course_faq = CourseFaq::where('course_id', $course->id)->get();
            }
        }

        if ($menu->is_content) {
            $content_list = Content::where('category_id', $menu->id)->get();
            $menu->contents = $content_list;

            foreach ($content_list as $content) {
                $content->content_outline = ContentOutline::select(
                    'content_outlines.*',
                    'class_levels.name as class_name',
                    'subjects.name as subject_name',
                    'chapters.name as chapter_name'
                )
                    ->where('content_outlines.content_id', $content->id)
                    ->leftJoin('class_levels', 'class_levels.id', 'content_outlines.class_level_id')
                    ->leftJoin('subjects', 'subjects.id', 'content_outlines.subject_id')
                    ->leftJoin('chapters', 'chapters.id', 'content_outlines.chapter_id')
                    ->get();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $menu
        ], 200);
    }

    public function mobileMenuList(Request $request)
    {
        $menus = Category::orderBy('sequence', 'ASC')->get();
        foreach ($menus as $item) {
            $sub_menu = [];

            if ($item->is_course) {
                $courses = Course::where('category_id', $item->id)->get();
                foreach ($courses as $course) {
                    array_push($sub_menu, ['sub_menu_id' => $course->id, 'sub_menu' => $course->title, 'sub_menu_bn' => $course->title_bn]);
                }
            }

            if ($item->is_content) {
                $content_list = Content::where('category_id', $item->id)->get();
                foreach ($content_list as $content) {
                    array_push($sub_menu, ['sub_menu_id' => $content->id, 'sub_menu' => $content->title, 'sub_menu_bn' => $content->title_bn]);
                }
            }

            $item->sub_menu = $sub_menu;
        }

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $menus
        ], 200);
    }

    public function websiteMenuList (Request $request)
    {
        $menus = Category::orderBy('sequence', 'ASC')->where('is_active', true)->get();
        foreach ($menus as $item) {
            $sub_menu = [];

            if($item->is_course){
                $courses = Course::where('category_id', $item->id)->get();
                foreach ($courses as $course) {
                    array_push($sub_menu, ['sub_menu_id' => $course->id, 'sub_menu' => $course->title, 'sub_menu_bn' => $course->title_bn]);
                }
            }

            if($item->is_content){
                $content_list = Content::where('category_id', $item->id)->get();
                foreach ($content_list as $content) {
                    array_push($sub_menu, ['sub_menu_id' => $content->id, 'sub_menu' => $content->title, 'sub_menu_bn' => $content->title_bn]);
                }
            }
            
            $item->sub_menu = $sub_menu;
        }

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $menus
        ], 200);
    }

    public function saveOrUpdateMenu(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'data' => $validateUser->errors()
                ], 422);
            }
            if ($request->id) {
                Category::where('id', $request->id)->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Menu has been updated successfully',
                    'data' => []
                ], 200);
            } else {
                $isExist = Category::where('name', $request->name)->first();
                if (empty($isExist)) {
                    Category::create($request->all());
                    return response()->json([
                        'status' => true,
                        'message' => 'Menu has been created successfully',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Menu already Exist!',
                        'data' => []
                    ], 200);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 200);
        }
    }

    public function adminCategoryList(Request $request)
    {
        $category_list = Category::all();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $category_list
        ], 200);
    }

    public function saveOrUpdateCategory(Request $request)
    {
        try {
            if ($request->id) {
                $type = Category::where('id', $request->id)->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Category has been updated successfully',
                    'data' => []
                ], 200);
            } else {
                $isExist = Category::where('name', $request->name)->first();
                if (empty($isExist)) {
                    $type = Category::create($request->all());
                    return response()->json([
                        'status' => true,
                        'message' => 'Category has been created successfully',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Category already Exist!',
                        'data' => []
                    ], 200);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 200);
        }
    }

    public function markGradeList(Request $request)
    {
        $grade = ['BelowSatisfaction', 'Satisfactory', 'Good', 'Better', 'Excellent'];

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $grade
        ], 200);
    }

    public function tagsList(Request $request)
    {
        $interest = Interest::pluck('tags');

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $interest
        ], 200);
    }

    public function saveOrUpdateTags(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'tag' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'data' => $validateUser->errors()
                ], 422);
            }
            if ($request->id) {
                Interest::where('id', $request->id)->update([
                    'tags' => $request->tag
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Tag has been updated successfully',
                    'data' => []
                ], 200);
            } else {
                $isExist = Interest::where('tags', $request->tag)->first();
                if (empty($isExist)) {
                    Interest::create([
                        'tags' => $request->tag
                    ]);
                    return response()->json([
                        'status' => true,
                        'message' => 'Tag has been created successfully',
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Tag already Exist!',
                        'data' => []
                    ], 200);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 200);
        }
    }

    public function websitePageList(Request $request, $id)
    {
        $pages = WebsitePage::where('organization_id', $id)->leftJoin('organizations', 'organizations.id', '=', 'website_pages.organization_id')->select(
                'website_pages.id',
                'website_pages.page_title',
                'website_pages.page_details',
                'website_pages.page_banner',
                'website_pages.organization_id',
                'organizations.name as organization_name'
            )->get();
        return $this->apiResponse($pages, 'Pages List', true, 200);
    }

    public function websitePageSaveOrUpdate(Request $request)
    {
        try {
            $page = [
                'page_title' => $request->page_title,
                'page_details' => $request->page_details,
                'organization_id' => $request->organization_id,
            ];
            if (empty($request->id)) {
                $pages = WebsitePage::create($page);

                if ($request->hasFile('page_banner')) {
                    $pages->update([
                        'page_banner' => $this->imageUpload($request, 'page_banner', 'banner'),
                    ]);
                }
                return $this->apiResponse([], 'Page has been created successfully', true, 201);
            } else {

                $pages = WebsitePage::where('id', $request->id)->first();
                $pages->update($page);
                if ($request->hasFile('page_banner')) {
                    $pages->update([
                        'page_banner' => $this->imageUpload($request, 'page_banner', 'banner', $pages->page_banner),
                    ]);
                }
                return $this->apiResponse([], 'Page has been updated successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        };
    }   
    
    public function tagsListAdmin(Request $request)
    {
        $interest = Interest::get();

        return response()->json([
            'status' => true,
            'message' => 'List Successful',
            'data' => $interest
        ], 200);
    }

    public function tagsSaveOrUpdateAdmin(Request $request)
    {
    try {
            if (empty($request->id)) {
                $tag = json_decode($request->tags, true);
                if ($tag) {
                    $tags = [];
                    foreach ($tag as $key => $value) {
                        $tags[] = [
                            'tags' => $value,
                        ];
                    }
                    Interest::insert($tags);
                }
                return $this->apiResponse([], 'Tags Created Successfully', true, 201);
            } else {
                $tags = Interest::where('id', $request->id)->first();
                $tags->update([
                    'tags' => $request->tags,
                ]);
                return $this->apiResponse([], 'Tags Updated Successfully', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function tagsDelete(Request $request,$id)
    {
        Interest::where('id', $id)->delete();
        return $this->apiResponse([], 'Tags Deleted Successfully', true, 200);
    }

}
