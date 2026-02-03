<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameLocation;
use App\Models\GameMode;
use App\Models\GameSlot;
use App\Models\GameSlotResult;
use Illuminate\Http\Request;

class GameSlotResultController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);

        $this->_module = 'Game Slot Result';
        $this->_routePrefix = 'game-slot-results';
        $this->_model = new GameSlotResult();
    }

    public function index(Request $request)
    {
        $this->initIndex();
        $srch_params = $request->all();
        $srch_params['with'] = ['location', 'slot', 'mode'];

        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;

        return view('admin.game_slot_results.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }

    public function create(Request $request)
    {
        return $this->__formUiGeneration($request);
    }

    public function store(Request $request)
    {
        return $this->__formPost($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->__formUiGeneration($request, $id);
    }

    public function update(Request $request, $id)
    {
        return $this->__formPost($request, $id);
    }

    public function destroy($id)
    {
        $response = $this->_model->remove($id);

        if ($response['status'] == 200) {
            return redirect()
                ->route($this->_routePrefix . '.index')
                ->with('success', $response['message']);
        } else {
            return redirect()
                ->route($this->_routePrefix . '.index')
                ->with('error', $response['message']);
        }
    }

    protected function __formUiGeneration(Request $request, $id = '')
    {
        $response = $this->initUIGeneration($id);
        if ($response) {
            return $response;
        }

        extract($this->_data);

        $locations = GameLocation::pluck('name', 'id')->toArray();
        $slots = [];
        $modes = [];

        if ($id && isset($data)) {
            if ($data->game_location_id) {
                $slots = GameSlot::where('game_id', $data->game_location_id)->pluck('name', 'id')->toArray();
            }
            if ($data->game_slot_id) {
                $modes = GameMode::where('slot_id', $data->game_slot_id)->pluck('name', 'id')->toArray();
            }
        }

        $form = [
            'route' => $this->_routePrefix . ($id ? '.update' : '.store'),
            'back_route' => route($this->_routePrefix . '.index'),
            'include_scripts' => $this->getDependentDropdownScript(),
            'fields' => [
                'game_location_id' => [
                    'type' => 'select',
                    'label' => 'Game Location',
                    'options' => $locations,
                    'value' => $id ? $data->game_location_id : null,
                    'attributes' => [
                        'required' => true,
                        'id' => 'game_location_id',
                        'class' => 'form-control select2',
                    ],
                ],
                'game_slot_id' => [
                    'type' => 'select',
                    'label' => 'Game Slot',
                    'options' => $slots,
                    'value' => $id ? $data->game_slot_id : null,
                    'attributes' => [
                        'required' => true,
                        'id' => 'game_slot_id',
                        'class' => 'form-control select2',
                    ],
                ],
                'game_mode_id' => [
                    'type' => 'select',
                    'label' => 'Game Mode',
                    'options' => $modes,
                    'value' => $id ? $data->game_mode_id : null,
                    'attributes' => [
                        'required' => true,
                        'id' => 'game_mode_id',
                        'class' => 'form-control select2',
                    ],
                ],
                'result_value' => [
                    'type' => 'text',
                    'label' => 'Result Value',
                    'value' => $id ? $data->result_value : null,
                    'attributes' => [
                        'required' => true,
                    ],
                ],
            ],
        ];

        return view('admin.components.admin-form', compact('data', 'id', 'form', 'breadcrumb', 'module'));
    }

    protected function __formPost(Request $request, $id = '')
    {
        $validationRules = [
            'game_location_id' => 'required',
            'game_slot_id' => 'required',
            'game_mode_id' => 'required',
            'result_value' => 'required',
        ];

        $this->validate($request, $validationRules);

        $input = $request->all();
        if (!$id) {
            $input['result_date'] = date('Y-m-d');
        }
        
        $response = $this->_model->store($input, $id);

        if (in_array($response['status'], [200, 201])) {
            return redirect()
                ->route($this->_routePrefix . '.index')
                ->with('success', $response['message']);
        } else {
            return redirect()
                ->route($this->_routePrefix . '.index')
                ->with('error', $response['message']);
        }
    }

    public function getSlots(Request $request)
    {
        $location_id = $request->input('location_id');
        $slots = GameSlot::where('game_id', $location_id)->pluck('name', 'id');
        return response()->json($slots);
    }

    public function getModes(Request $request)
    {
        $slot_id = $request->input('slot_id');
        $modes = GameMode::where('slot_id', $slot_id)->pluck('name', 'id');
        return response()->json($modes);
    }

    private function getDependentDropdownScript()
    {
        return '<script type="text/javascript">
            $(document).ready(function () {
                $("#game_location_id").change(function () {
                    var locationId = $(this).val();
                    $("#game_slot_id").empty().append("<option value=\'\'>Select Slot</option>");
                    $("#game_mode_id").empty().append("<option value=\'\'>Select Mode</option>");
                    
                    if (locationId) {
                        $.ajax({
                            url: "' . route("get-slots-by-location") . '",
                            type: "GET",
                            data: { location_id: locationId },
                            success: function (data) {
                                $.each(data, function (key, value) {
                                    $("#game_slot_id").append("<option value=\'" + key + "\'>" + value + "</option>");
                                });
                            }
                        });
                    }
                });

                $("#game_slot_id").change(function () {
                    var slotId = $(this).val();
                    $("#game_mode_id").empty().append("<option value=\'\'>Select Mode</option>");
                    
                    if (slotId) {
                        $.ajax({
                            url: "' . route("get-modes-by-slot") . '",
                            type: "GET",
                            data: { slot_id: slotId },
                            success: function (data) {
                                $.each(data, function (key, value) {
                                    $("#game_mode_id").append("<option value=\'" + key + "\'>" + value + "</option>");
                                });
                            }
                        });
                    }
                });
            });
        </script>';
    }
}
