<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\CapacitacionesCore\App\Http\Api\capApiControllerAuthCapacitaciones;
use Modules\CapacitacionesCore\app\Http\Api\capApiControllerCapacitaciones;
use Modules\CapacitacionesCore\app\Http\Api\capApiControllerInstructorCapacitaciones;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('capacitacionescore', CapacitacionesCoreController::class)->names('capacitacionescore');
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando correctamente']);
});

Route::prefix('capacitaciones')->group(function () {

    Route::post('register',                 [capApiControllerAuthCapacitaciones::class, 'register']);
    Route::post('login',                    [capApiControllerAuthCapacitaciones::class, 'login']);
    Route::post('forgot-password',          [capApiControllerAuthCapacitaciones::class, 'forgotPassword']);
    Route::post('reset-password',           [capApiControllerAuthCapacitaciones::class, 'resetPassword']);

    // Route::middleware('auth:api')->group(function () {
    //     Route::post('logout',                   [capApiControllerAuthCapacitaciones::class, 'logout']);
    //     Route::post('verify-2fa',               [capApiControllerAuthCapacitaciones::class, 'verify2FA']);
    //     Route::post('disable-2fa',              [capApiControllerAuthCapacitaciones::class, 'disable2FA']);
    //     Route::post('enable-2fa',               [capApiControllerAuthCapacitaciones::class, 'enable2FA']);
    //     Route::get('me', [capApiControllerAuthCapacitaciones::class, 'me']);
    // });

    Route::get('/capLastcourse', [capApiControllerCapacitaciones::class, 'capFunctionUltimoCurso']);
    Route::get('/capInscribedcourses', [capApiControllerCapacitaciones::class, 'capFunctionCursosInscrito']);
    Route::get('/capCoursecatalogue', [capApiControllerCapacitaciones::class, 'capFunctionCatalogoCursos']);
    Route::get('/capCourseinfo/{id}', [capApiControllerCapacitaciones::class, 'capFunctionInformacionCurso']);
    Route::get('capStudentcourse/{course}/evaluation/{evaluation}', [capApiControllerCapacitaciones::class, 'capFunctionCursoEvaluacion']);
    Route::get('/capStudentcourse/{id}', [capApiControllerCapacitaciones::class, 'capFunctionCursoEstudiante']);
    Route::post('/capStudentevaluation/answers', [capApiControllerCapacitaciones::class, 'capFunctionRespuestasCursoEvaluacion']);
});

Route::prefix('instructorCapacitaciones')->group(function () {
    Route::get('/capIndexCourse', [capApiControllerInstructorCapacitaciones::class, 'capFunctionIndexCurso']);
    Route::get('/capCreateCourse', [capApiControllerInstructorCapacitaciones::class, 'capFunctionCreateCurso']);
    Route::post('/capStoreCourse', [capApiControllerInstructorCapacitaciones::class, 'capFunctionStoreCurso']);
    Route::get('/capEditCourse/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionEditCurso']);
    Route::post('/capUpdateCourse/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionUpdateCurso']);
    Route::get('/capShowCourse/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionShowCurso']);
    Route::delete('/capDeleteCourse/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDeleteCurso']);

    Route::get('/capIndexGoals/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionIndexGoals']);
    Route::post('/capStoreGoals/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionStoreGoals']);
    Route::get('/capEditGoals/{id_goal}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionEditGoals']);
    Route::post('/capUpdateGoals/{id_goal}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionUpdateGoals']);
    Route::delete('/capDeleteGoals/{id_goal}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDeleteGoals']);

    Route::get('/capIndexRequirements/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionIndexRequirements']);
    Route::post('/capStoreRequirements/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionStoreRequirements']);
    Route::get('/capEditRequirements/{id_requirement}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionEditRequirements']);
    Route::post('/capUpdateRequirements/{id_requirement}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionUpdateRequirements']);
    Route::delete('/capDeleteRequirements/{id_requirement}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDeleteRequirements']);

    Route::get('/capIndexAudience/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionIndexAudience']);
    Route::post('/capStoreAudience/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionStoreAudience']);
    Route::get('/capEditAudience/{id_audience}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionEditAudience']);
    Route::post('/capUpdateAudience/{id_audience}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionUpdateAudience']);
    Route::delete('/capDeleteAudience/{id_audience}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDeleteAudience']);

    Route::get('/capIndexEstudiantes/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionIndexEstudiantes']);
    Route::post('/capStoreEstudiantes/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionStoreEstudiantes']);
    Route::delete('/capDeleteEstudiantes', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDeleteEstudiantes']);
    Route::delete('/capDeleteMultipleEstudiantes', [capApiControllerInstructorCapacitaciones::class, 'capFunctionMultipleDeleteEstudiantes']);
    Route::delete('/capDeleteAllEstudiantes/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionAllDeleteEstudiantes']);

    Route::get('/capIndexSections/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionIndexSeccionesCurso']);
    Route::post('/capStoreSections/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionStoreSeccionesCurso']);
    Route::delete('/capDeleteSection/{id_section}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDeleteSeccion']);
    Route::delete('/capDeleteLesson/{id_lesson}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDeleteLesson']);

    Route::get('/capIndexEvaluations/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionIndexEvaluacion']);

    Route::get('/capIndexEvaluation/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionIndexEvaluacion']);
    Route::get('/capCreateEvaluation/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionCreateEvaluacion']);
    Route::post('/capStoreEvaluation/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionStoreEvaluacion']);
    Route::get('/capEditEvaluation/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionEditarEvaluacion']);
    Route::post('/capUpdateEvaluation/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionUpdateEvaluacion']);
    Route::delete('/capDeleteEvaluation/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDestroyEvaluacion']);

    Route::get('/capEvaluationQuestions/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionEvaluacionwithQuestions']);
    Route::post('/capStoreQuestion/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionStoreQuestion']);
    Route::get('/capEditQuestion/{id_question}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionEditarQuestion']);
    Route::get('/capUpdateQuestion/{id_question}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionUpdateQuestion']);
    Route::post('/capDeleteQuestion/{id_question}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDeleteQuestion']);
    Route::delete('/capDeleteAnswer/{id_answer}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionDeleteAnswer']);

    Route::get('/capEditCertificadoCurso/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionEditCertificadoCurso']);
    Route::post('/capUpdateCertificadoCurso/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'capFunctionUpdateCertificadoCurso']);
});
