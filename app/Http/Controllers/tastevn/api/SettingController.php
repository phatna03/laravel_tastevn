<?php

namespace App\Http\Controllers\tastevn\api;

use App\Api\SysCore;
use App\Http\Controllers\Controller;
use App\Models\RestaurantFood;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\SysSetting;

class SettingController extends Controller
{
  public function __construct()
  {
    $this->middleware(function ($request, $next) {
      return $next($request);
    });

    $this->middleware('auth');
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $user = Auth::user();
    $invalid_roles = ['moderator', 'user'];
    if (in_array($user->role, $invalid_roles)) {
      return redirect('page_not_found');
    }

    $settings = [];

    $rows = SysSetting::all();
    if (count($rows)) {
      foreach ($rows as $row) {
        $settings[$row->key] = $row->value;
      }
    }

    $pageConfigs = [
      'myLayout' => 'horizontal',
      'hasCustomizer' => false,

      'settings' => $settings
    ];

    $user->add_log([
      'type' => 'view_listing_setting',
    ]);

    return view('tastevn.pages.settings', ['pageConfigs' => $pageConfigs]);
  }

  public function create(Request $request)
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request)
  {
    $values = $request->all();
    $user = Auth::user();
    $api_core = new SysCore();

    $diffs['before'] = $api_core->get_log_settings();

    if (count($values)) {
      $this->save_settings($values);
    }

    $diffs['after'] = $api_core->get_log_settings();
    if (json_encode($diffs['before']) !== json_encode($diffs['after'])) {
      $user->add_log([
        'type' => 'edit_setting',
        'params' => json_encode($diffs),
      ]);
    }

    return response()->json([
      'status' => true,
    ], 200);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }

  protected function save_settings($settings = [])
  {
    foreach ($settings as $key => $value) {
      SysSetting::updateOrCreate([
        'key' => $key,
      ],
      [
        'value' => $value,
      ]);
    }
  }
}
