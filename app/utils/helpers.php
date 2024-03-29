<?php

namespace App\utils;

use App\Models\Currency;
use App\Models\Role;
use App\Models\Setting;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class helpers
{

    //  Helper Multiple Filter
    public function filter($model, $columns, $param, $request)
    {
        // Loop through the fields checking if they've been input, if they have add
        //  them to the query.
        $fields = [];
        for ($key = 0; $key < count($columns); $key++) {
            $fields[$key]['param'] = $param[$key];
            $fields[$key]['value'] = $columns[$key];
        }

        foreach ($fields as $field) {
            $model->where(function ($query) use ($request, $field, $model) {
                return $model->when($request->filled($field['value']),
                    function ($query) use ($request, $model, $field) {
                        $field['param'] = 'like' ?
                            $model->where($field['value'], 'like', "{$request[$field['value']]}")
                            : $model->where($field['value'], $request[$field['value']]);
                    });
            });
        }

        // Finally return the model
        return $model;
    }

    //  Check If Hass Permission
    public static function checkPermissionJson(string $permission)
    {
        $user = Auth::user();
        if (!$user->can($permission)) {
           return response()->json(['error' => "No tiene permisos"], 406);
        }
    }

    public static function checkPermission(string $permission)
    {
        $user = Auth::user();
        return $user->can($permission);
    }

    ////  Check If Hass Permission Show All records
    public function Show_Records($model)
    {
        $Role = Auth::user()->roles()->first();
        $ShowRecord = Role::findOrFail($Role->id)->inRole('record_view');

        if (!$ShowRecord) {
            return $model->where('user_id', '=', Auth::user()->id);
        }
        return $model;
    }

    // Get Currency
    public function Get_Currency()
    {
        $settings = Setting::with('Currency')->where('deleted_at', '=', null)->first();

        if ($settings && $settings->currency_id) {
            if (Currency::where('id', $settings->currency_id)
                ->where('deleted_at', '=', null)
                ->first()) {
                $symbol = $settings['Currency']->symbol;
            } else {
                $symbol = '';
            }
        } else {
            $symbol = '';
        }
        return $symbol;
    }

    // Get Currency COde
    public function Get_Currency_Code()
    {
//        $settings = Setting::with('Currency')->where('deleted_at', '=', null)->first();
        $code = 'Bs';
//        if ($settings && $settings->currency_id) {
//            if (Currency::where('id', $settings->currency_id)
//                ->where('deleted_at', '=', null)
//                ->first()) {
//                $code = $settings['Currency']->code;
//            }
//        }
        return $code;
    }

    public static function to_select_vuetify(Collection $data)
    {
        return $data->map(function ($item, $key) {
            return ['value' => $item->id, 'title' => $item->name];
        });
    }

    public static function get_code($ref, $sub_code)
    {
        $dateYear = Carbon::now()->format("y");
        $code = "{$sub_code}_{$dateYear}_1001";
        if (isset($ref)) {
            $nwMsg = Str::of($ref)->explode("_");
            if ($nwMsg->count() > 1) {
                $year = $nwMsg->get(1);
                $number = $nwMsg->get(2) + 1;
                if ($dateYear != $year) {
                    $year = $dateYear;
                    $number = 1001;
                }
                $code = "{$nwMsg[0]}_{$year}_{$number}";
            }
        }
        return $code;
    }

    public static function getWarehouses($user_auth)
    {
        if ($user_auth->is_all_warehouses) {
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
        }
        return $warehouses;
    }

    public static function getFilter(\Illuminate\Support\Collection $filter, $Model, \Illuminate\Support\Collection $filters)
    {
        if ($filter->count() > 0) {
            $filterData = false;
            foreach ($filters as $data) {
                switch ($data->get('type')) {
                    case 'date':
                        if (!empty($filter->get($data->get('filter'))) && $filter->get($data->get('filter2'))) {
                            $Model = $Model->whereBetween($data->get('key'), [Carbon::parse($filter->get($data->get('filter'))), Carbon::parse($filter->get($data->get('filter2')))]);
                        }
                        break;
                    case 'string':
                        if (!empty($filter->get($data->get('filter')))) {
                            $Model = $Model->where($data->get('key'), 'like', "%{$filter->get($data->get('filter'))}%");
                        }
                        break;
                    case 'rel':
                        if (!empty($filter->get($data->get('filter')))) {
                            $Model = $Model->whereHas($data->get('key'), function (Builder $query) use ($data, $filter) {
                                $query->where($data->get('key2'), '=', $filter->get($data->get('filter')));
                            });
                        }
                        break;
                    case 'rel_string':
                        if (!empty($filter->get($data->get('filter')))) {
                            $Model = $Model->whereHas($data->get('key'), function (Builder $query) use ($data, $filter) {
                                $query->where($data->get('key2'), 'like', "%{$filter->get($data->get('filter'))}%");
                            });
                        }
                        break;
                    default:
                        if (!empty($filter->get($data->get('filter')))) {
                            $Model = $Model->where($data->get('key'), '=', $filter->get($data->get('filter')));
                        }
                        break;
                }
                $filterData = true;
            }

            if (!$filterData) {
                $Model = $Model->limit(1000);
            }
        } else {
            $Model = $Model->limit(500);
        }
        return $Model;
    }
}
