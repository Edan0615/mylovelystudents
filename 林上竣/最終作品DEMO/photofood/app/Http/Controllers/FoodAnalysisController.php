<?php

namespace App\Http\Controllers;

use App\Models\FoodAnalysis;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FoodAnalysisController extends Controller
{
    /**
     * 分析食物圖片並計算熱量
     */
    public function analyzeFood(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 最大 10MB
        ]);

        try {
            // 儲存上傳的圖片
            $imagePath = $request->file('image')->store('food_images', 'public');
            $fullImagePath = storage_path('app/public/' . $imagePath);
            
            // 將圖片轉換為 base64
            $imageData = base64_encode(file_get_contents($fullImagePath));
            
            // 使用 OpenAI Vision API 分析圖片
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o', // 或 'gpt-4o' 如果可用
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => '請仔細分析這張食物圖片。請用繁體中文回答以下問題：
                                1. 圖片中有哪些食物？
                                2. 每種食物的估計份量（例如：一碗、一盤、一個等）
                                3. 每種食物的估計熱量（大卡/卡路里）
                                4. 總熱量估計
                                5. 其他營養資訊（如果可能的話）
                                6. 將每種食物的熱量放在一個array裡面 要有名字跟熱量 例如：{"name": "雞肉", "calories": 100} 讓他可以後續被應用 

                                請以結構化的方式回答，格式要清楚易讀。'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:image/jpeg;base64,' . $imageData,
                                ],
                            ],
                        ],
                    ],
                ],
                'max_tokens' => 1000,
            ]);

            $analysis = $response->choices[0]->message->content;

            // 嘗試解析 JSON 格式的食物陣列
            $foods = null;
            $totalCalories = null;
            try {
                // 方法1: 嘗試從分析結果中提取 JSON 陣列
                if (preg_match('/\[[\s\S]*?\]/', $analysis, $matches)) {
                    $foods = json_decode($matches[0], true);
                }
                
                // 方法2: 如果方法1失敗，嘗試提取多個 JSON 對象
                if (!$foods && preg_match_all('/\{[^}]*"name"[^}]*"calories"[^}]*\}/', $analysis, $matches)) {
                    $foods = [];
                    foreach ($matches[0] as $match) {
                        $decoded = json_decode($match, true);
                        if ($decoded && isset($decoded['name']) && isset($decoded['calories'])) {
                            $foods[] = $decoded;
                        }
                    }
                }
                
                // 方法3: 嘗試提取整個 JSON 結構
                if (!$foods && preg_match('/\{[\s\S]*"foods"[\s\S]*\}/', $analysis, $matches)) {
                    $decoded = json_decode($matches[0], true);
                    if ($decoded && isset($decoded['foods'])) {
                        $foods = $decoded['foods'];
                    }
                }
                
                if ($foods && is_array($foods) && count($foods) > 0) {
                    // 確保所有項目都有 name 和 calories
                    $foods = array_filter($foods, function($item) {
                        return isset($item['name']) && isset($item['calories']);
                    });
                    $foods = array_values($foods); // 重新索引
                    
                    // 計算總熱量
                    $totalCalories = array_sum(array_column($foods, 'calories'));
                }
            } catch (\Exception $e) {
                Log::warning('無法解析食物陣列: ' . $e->getMessage());
            }

            // 儲存到資料庫
            $foodAnalysis = FoodAnalysis::create([
                'user_id' => Auth::id(),
                'image_path' => $imagePath,
                'analysis' => $analysis,
                'foods' => $foods,
                'total_calories' => $totalCalories,
            ]);

            // 返回分析結果
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'image_url' => Storage::url($imagePath),
                'foods' => $foods,
                'total_calories' => $totalCalories,
                'id' => $foodAnalysis->id,
            ]);

        } catch (\Exception $e) {
            Log::error('食物分析錯誤: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '分析失敗：' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 使用圖片 URL 分析（如果圖片已經在網路上）
     */
    public function analyzeFoodByUrl(Request $request)
    {
        $request->validate([
            'image_url' => 'required|url',
        ]);

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4-vision-preview',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => '請仔細分析這張食物圖片。請用繁體中文回答以下問題：
1. 圖片中有哪些食物？
2. 每種食物的估計份量
3. 每種食物的估計熱量（大卡）
4. 總熱量估計
5. 其他營養資訊

請以結構化的方式回答。'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => $request->image_url,
                                ],
                            ],
                        ],
                    ],
                ],
                'max_tokens' => 1000,
            ]);

            $analysis = $response->choices[0]->message->content;

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);

        } catch (\Exception $e) {
            Log::error('食物分析錯誤: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '分析失敗：' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * 獲取每日熱量數據（API）
     */
    public function getDailyCalories(Request $request)
    {
        $days = $request->input('days', 30); // 預設顯示最近 30 天
        
        // 查詢指定天數內的數據
        $startDate = now()->subDays($days)->startOfDay();
        
        $dailyCalories = FoodAnalysis::where('user_id', Auth::id())
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('total_calories')
            ->selectRaw('DATE(created_at) as date, SUM(total_calories) as total_calories')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'calories' => (float) $item->total_calories,
                ];
            });

        // 填充缺失的日期（設為 0）
        $result = [];
        $currentDate = $startDate->copy();
        $endDate = now()->endOfDay();
        $caloriesMap = $dailyCalories->pluck('calories', 'date')->toArray();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $result[] = [
                'date' => $dateStr,
                'calories' => $caloriesMap[$dateStr] ?? 0,
            ];
            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * 顯示所有分析記錄列表
     */
    public function index()
    {
        $analyses = FoodAnalysis::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // 如果是 AJAX 請求，返回 JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $analyses->items(),
                'pagination' => [
                    'current_page' => $analyses->currentPage(),
                    'last_page' => $analyses->lastPage(),
                    'per_page' => $analyses->perPage(),
                    'total' => $analyses->total(),
                ]
            ]);
        }

        return view('food-analyses.index', compact('analyses'));
    }

    /**
     * 刪除分析記錄
     */
    public function destroy($id)
    {
        $analysis = FoodAnalysis::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        // 刪除圖片
        if ($analysis->image_path && Storage::disk('public')->exists($analysis->image_path)) {
            Storage::disk('public')->delete($analysis->image_path);
        }

        // 刪除記錄
        $analysis->delete();

        return response()->json([
            'success' => true,
            'message' => '記錄已刪除',
        ]);
    }
}

