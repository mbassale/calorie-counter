<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Meal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MealsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Meal::class);
        $user = $request->user();
        $query = Meal::query()->with('user');
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        if ($request->has('startDate')) {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $query->where('date', '>=', $startDate);
        }
        if ($request->has('startTime')) {
            $query->whereRaw("TIME_TO_SEC(TIME(`date`)) >= TIME_TO_SEC('{$request->startTime}:00')");
        }
        if ($request->has('endDate')) {
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $query->where('date', '<=', $endDate);
        }
        if ($request->has('endTime')) {
            $query->whereRaw("TIME_TO_SEC(TIME(`date`)) <=  TIME_TO_SEC('{$request->endTime}:00')");
        }
        return $query->orderBy('date', 'desc')->get();
    }

    public function show(Request $request, Meal $meal = null)
    {
        $this->authorize('view', $meal);
        $meal->load('user');
        return $meal;
    }

    public function store(Request $request)
    {
        $this->authorize('create', Meal::class);
        $this->validate($request, [
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required',
            'date' => 'required',
            'calories' => 'required'
        ]);
        $mealData = $request->only(['user_id', 'name', 'date', 'calories']);
        $user = $request->user();
        if ($user->isUser() || !$mealData['user_id']) {
            $mealData['user_id'] = $user->id;
        }
        $meal = Meal::create($mealData);
        $meal->load('user');
        return $meal;
    }

    public function update(Request $request, Meal $meal)
    {
        $this->authorize('update', $meal);
        $this->validate($request, [
            'name' => 'required',
            'date' => 'required',
            'calories' => 'required'
        ]);
        $meal->update($request->only(['name', 'date', 'calories']));
        $meal->load('user');
        return $meal;
    }

    public function destroy(Meal $meal)
    {
        $this->authorize('delete', $meal);
        $meal->load('user');
        $meal->delete();
        return $meal;
    }
}
