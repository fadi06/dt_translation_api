<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TranslationRequest;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class TranslationController extends Controller
{
    use ApiResponse;

    protected TranslationRepository $translationRepository;

    public function __construct(TranslationRepository $translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/translations",
     *     tags={"Translations"},
     *     summary="Translations list",
     *     description="Translation listing.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         required=true,
     *         description="Locale to filter translations by",
     *
     *         @OA\Schema(type="string", example="en")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Translations retrieved successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translations retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="key", type="string", example="in-quae-amet-sequi-possimus-inventore-expedita"),
     *                         @OA\Property(property="content", type="string", example="Et voluptatem rerum accusantium quis aut illum ut dolorem."),
     *                         @OA\Property(property="locale", type="string", example="en"),
     *                         @OA\Property(
     *                             property="tags",
     *                             type="array",
     *
     *                             @OA\Items(
     *                                 type="object",
     *
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="name", type="string", example="mobile")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost:6266/api/translations?page=1"),
     *                 @OA\Property(property="last_page", type="integer", example=83264),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost:6266/api/translations?page=83264"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://localhost:6266/api/translations?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="total", type="integer", example=166528)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $translations = $this->translationRepository->getAll($request->all());

        return $this->sendSuccess($translations, 'Translations retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     tags={"Translations"},
     *     summary="Create a new translation",
     *     description="Creates a new translation entry with key, locale, content, and tag.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Translation creation payload",
     *
     *         @OA\JsonContent(
     *             required={"key", "locale", "content", "tag"},
     *
     *             @OA\Property(property="key", type="string", example="mobile button"),
     *             @OA\Property(property="locale", type="string", example="en"),
     *             @OA\Property(property="tag", type="string", example="tag 1"),
     *             @OA\Property(property="content", type="string", example="this is for testing")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Translation created successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translation created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=500002),
     *                 @OA\Property(property="key", type="string", example="mobile button"),
     *                 @OA\Property(property="locale_id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="this is for testing"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-19T16:06:07.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-19T16:06:07.000000Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties=@OA\Property(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function store(TranslationRequest $request): JsonResponse
    {
        $translation = $this->translationRepository->create($request->validated());

        return $this->sendSuccess($translation, 'Translation created successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/translations/{id}",
     *     tags={"Translations"},
     *     summary="Get a single translation",
     *     description="Retrieves a single translation record by its ID.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the translation",
     *
     *         @OA\Schema(type="integer", example=50000)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Translation retrieved successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translation retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=50000),
     *                 @OA\Property(property="key", type="string", example="et-nihil-odit-odio-facilis"),
     *                 @OA\Property(property="locale_id", type="integer", example=3),
     *                 @OA\Property(property="content", type="string", example="Nostrum illo quo esse et magni rem cupiditate."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", nullable=true, example=null),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Translation not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function show(Translation $translation): JsonResponse
    {
        $translation = $this->translationRepository->find($translation);

        return $this->sendSuccess($translation, 'Translation retrieved successfully.');
    }

    /**
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     tags={"Translations"},
     *     summary="Update an existing translation",
     *     description="Updates the specified translation entry by ID.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the translation to update",
     *
     *         @OA\Schema(type="integer", example=500002)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Translation update payload",
     *
     *         @OA\JsonContent(
     *             required={"key", "locale", "content", "tag"},
     *
     *             @OA\Property(property="key", type="string", example="mobile button"),
     *             @OA\Property(property="locale", type="string", example="en"),
     *             @OA\Property(property="tag", type="string", example="tag 1"),
     *             @OA\Property(property="content", type="string", example="this is for testing")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translation updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=500002),
     *                 @OA\Property(property="key", type="string", example="mobile button"),
     *                 @OA\Property(property="locale_id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="this is for testing"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-19T16:06:07.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-19T16:06:07.000000Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties=@OA\Property(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function update(TranslationRequest $request, Translation $translation): JsonResponse
    {
        $translation = $this->translationRepository->update($translation, $request->validated());

        return $this->sendSuccess($translation, 'Translation updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     tags={"Translations"},
     *     summary="Delete a translation",
     *     description="Deletes the specified translation by ID.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the translation to delete",
     *
     *         @OA\Schema(type="integer", example=500001)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Translation deleted successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translation deleted successfully."),
     *             @OA\Property(property="data", type="string", nullable=true, example=null)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Translation not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function destroy(Translation $translation): JsonResponse
    {
        $this->translationRepository->delete($translation);

        return $this->sendSuccess(null, 'Translation deleted successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/translations/export",
     *     tags={"Translations"},
     *     summary="Export translations",
     *     description="Exports translations based on the given locale and optional tag.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         required=true,
     *         description="Locale code for translations",
     *
     *         @OA\Schema(type="string", example="en")
     *     ),
     *
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         required=false,
     *         description="Optional tag to filter translations",
     *
     *         @OA\Schema(type="string", example="mobile")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Translations exported successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translations exported successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="key", type="string", example="in-quae-amet-sequi-possimus-inventore-expedita"),
     *                     @OA\Property(property="content", type="string", example="Et voluptatem rerum accusantium quis aut illum ut dolorem."),
     *                     @OA\Property(property="locale", type="string", example="en"),
     *                     @OA\Property(
     *                         property="tags",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="mobile")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function export(string $locale, ?string $tag = null): JsonResponse
    {
        $translations = $this->translationRepository->export($locale, $tag);

        return $this->sendSuccess($translations, 'Translations exported successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/translations/search",
     *     tags={"Translations"},
     *     summary="Search translations",
     *     description="Searches translations based on the provided query string.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="Search keyword to filter translations",
     *         @OA\Schema(type="string", example="button")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translations exported successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translations exported successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="key", type="string", example="save-button"),
     *                         @OA\Property(property="content", type="string", example="Save"),
     *                         @OA\Property(property="locale", type="string", example="en"),
     *                         @OA\Property(
     *                             property="tags",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="name", type="string", example="mobile")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/translations/search?page=1"),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/translations/search?page=5"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://localhost:8000/api/translations/search?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        if (!$request->has('query')) {
            return $this->sendError('Query parameter is required.');
        }
        $translations = $this->translationRepository->search($request->input('query'));

        return $this->sendSuccess($translations, 'Translations exported successfully.');
    }
}
