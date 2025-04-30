<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
    Route::get('/tblastcourse', [capApiControllerCapacitaciones::class, 'tbFunctionUltimoCurso']);
    Route::get('/tbinscribedcourses', [capApiControllerCapacitaciones::class, 'tbFunctionCursosInscrito']);
    Route::get('/tbcoursecatalogue', [capApiControllerCapacitaciones::class, 'tbFunctionCatalogoCursos']);
    Route::get('/tbcourseinfo/{id}', [capApiControllerCapacitaciones::class, 'tbFunctionInformacionCurso']);
    Route::get('tbstudentcourse/{course}/evaluation/{evaluation}', [capApiControllerCapacitaciones::class, 'tbFunctionCursoEvaluacion']);
    Route::get('/tbstudentcourse/{id}', [capApiControllerCapacitaciones::class, 'tbFunctionCursoEstudiante']);
    Route::post('/tbstudentevaluation/answers', [capApiControllerCapacitaciones::class, 'tbFunctionRespuestasCursoEvaluacion']);
});

Route::prefix('instructorCapacitaciones')->group(function () {
    Route::get('/tbIndexCourse', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionIndexCurso']);
    Route::get('/tbCreateCourse', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionCreateCurso']);
    Route::post('/tbStoreCourse', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionStoreCurso']);
    Route::get('/tbEditCourse/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionEditCurso']);
    Route::post('/tbUpdateCourse/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionUpdateCurso']);
    Route::get('/tbShowCourse/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionShowCurso']);
    Route::delete('/tbDeleteCourse/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDeleteCurso']);

    Route::get('/tbIndexGoals/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionIndexGoals']);
    Route::post('/tbStoreGoals/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionStoreGoals']);
    Route::get('/tbEditGoals/{id_goal}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionEditGoals']);
    Route::post('/tbUpdateGoals/{id_goal}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionUpdateGoals']);
    Route::delete('/tbDeleteGoals/{id_goal}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDeleteGoals']);

    Route::get('/tbIndexRequirements/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionIndexRequirements']);
    Route::post('/tbStoreRequirements/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionStoreRequirements']);
    Route::get('/tbEditRequirements/{id_requirement}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionEditRequirements']);
    Route::post('/tbUpdateRequirements/{id_requirement}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionUpdateRequirements']);
    Route::delete('/tbDeleteRequirements/{id_requirement}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDeleteRequirements']);

    Route::get('/tbIndexAudience/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionIndexAudience']);
    Route::post('/tbStoreAudience/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionStoreAudience']);
    Route::get('/tbEditAudience/{id_audience}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionEditAudience']);
    Route::post('/tbUpdateAudience/{id_audience}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionUpdateAudience']);
    Route::delete('/tbDeleteAudience/{id_audience}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDeleteAudience']);

    Route::get('/tbIndexEstudiantes/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionIndexEstudiantes']);
    Route::post('/tbStoreEstudiantes/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionStoreEstudiantes']);
    Route::delete('/tbDeleteEstudiantes', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDeleteEstudiantes']);
    Route::delete('/tbDeleteMultipleEstudiantes', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionMultipleDeleteEstudiantes']);
    Route::delete('/tbDeleteAllEstudiantes/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionAllDeleteEstudiantes']);

    Route::get('/tbIndexSections/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionIndexSeccionesCurso']);
    Route::post('/tbStoreSections/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionStoreSeccionesCurso']);
    Route::delete('/tbDeleteSection/{id_section}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDeleteSeccion']);
    Route::delete('/tbDeleteLesson/{id_lesson}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDeleteLesson']);

    Route::get('/tbIndexEvaluations/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionIndexEvaluacion']);

    Route::get('/tbIndexEvaluation/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionIndexEvaluacion']);
    Route::get('/tbCreateEvaluation/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionCreateEvaluacion']);
    Route::post('/tbStoreEvaluation/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionStoreEvaluacion']);
    Route::get('/tbEditEvaluation/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionEditarEvaluacion']);
    Route::post('/tbUpdateEvaluation/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionUpdateEvaluacion']);
    Route::delete('/tbDeleteEvaluation/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDestroyEvaluacion']);

    Route::get('/tbEvaluationQuestions/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionEvaluacionwithQuestions']);
    Route::post('/tbStoreQuestion/{id_evaluation}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionStoreQuestion']);
    Route::get('/tbEditQuestion/{id_question}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionEditarQuestion']);
    Route::get('/tbUpdateQuestion/{id_question}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionUpdateQuestion']);
    Route::post('/tbDeleteQuestion/{id_question}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDeleteQuestion']);
    Route::delete('/tbDeleteAnswer/{id_answer}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionDeleteAnswer']);

    Route::get('/tbEditCertificadoCurso/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionEditCertificadoCurso']);
    Route::post('/tbUpdateCertificadoCurso/{id_course}', [capApiControllerInstructorCapacitaciones::class, 'tbFunctionUpdateCertificadoCurso']);
});
