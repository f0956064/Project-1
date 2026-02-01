<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameLocation;
use App\Models\GameSlot;
use App\Models\GameMode;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);
        
        $this->_module      = 'Game Location';
        $this->_routePrefix = 'game.locations';
        $this->_model       = new GameLocation();
    }

    /**
     * Display a listing of game locations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->initIndex();
        $srch_params = $request->all();
        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;
        $this->_data['filters'] = $this->_model->getFilters();
        return view('admin.game.locations.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }

    /**
     * Show the form for creating a new game location.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $this->__formUiGeneration($request);
    }

    /**
     * Store a newly created game location in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->__formPost($request);
    }

    /**
     * Show the form for editing the specified game location.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        return $this->__formUiGeneration($request, $id);
    }

    /**
     * Update the specified game location in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->__formPost($request, $id);
    }

    /**
     * Remove the specified game location from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->_model->remove($id);

        if($response['status'] == 200) {
            return redirect()
                ->route($this->_routePrefix . '.index')
                ->with('success', $response['message']);
        } else {
            return redirect()
                    ->route($this->_routePrefix . '.index')
                    ->with('error', $response['message']);
        }
    }

    /**
     * Toggle is_active status via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $data = $this->_model->getListing(['id' => $id]);
            if (!$data) {
                return response()->json(['status' => 'error', 'message' => 'Record not found'], 404);
            }
            
            $data->is_active = $data->is_active == 1 ? 0 : 1;
            $data->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
                'is_active' => $data->is_active
            ]);
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ui parameters for form add and edit
     *
     * @param  Request $request
     * @param  string $id
     * @return [type]
     */
    protected function __formUiGeneration(Request $request, $id = '')
    {
        $response = $this->initUIGeneration($id);
        if($response) {
            return $response;
        }

        extract($this->_data);
        
        // Get logo file if exists
        $logoValue = null;
        if ($id && isset($data->logo) && $data->logo) {
            $logoFileModel = \App\Models\File::find($data->logo);
            if ($logoFileModel) {
                $logoValue = \App\Models\File::file($logoFileModel);
            }
        }

        $form = [
            'route'      => $this->_routePrefix . ($id ? '.update' : '.store'),
            'back_route' => route($this->_routePrefix . '.index'),
            'fields'     => [
                'name'      => [
                    'type'          => 'text',
                    'label'         => 'Name',
                    'attributes'    => [
                        'required'  => true
                    ]
                ],
                'logo'        => [
                    'type'          => 'file',
                    'label'         => 'Logo',
                    'value'         => $logoValue,
                    'attributes'    => [
                        'accept'    => 'image/*',
                    ]
                ],
            ],
        ];

        return view('admin.components.admin-form', compact('data', 'id', 'form', 'breadcrumb', 'module'));
    }

    /**
     * Form post action
     *
     * @param  Request $request
     * @param  string  $id
     * @return [type]
     */
    protected function __formPost(Request $request, $id = '')
    {
        $validationRules = [
            'name'          => 'required|max:255',
        ];

        $this->validate($request, $validationRules);

        $input      = $request->all();
        unset($input['logo']); // Remove logo from input as it's handled separately
        $response   = $this->_model->store($input, $id, $request);
        
        if (in_array($response['status'], [200, 201])) {
            return redirect()
                ->route($this->_routePrefix . '.index')
                ->with('success',  $response['message']);
        } else {
            return redirect()
                    ->route($this->_routePrefix . '.index')
                    ->with('error', $response['message']);
        }
    }

    // ============ Game Slots Methods ============

    /**
     * Display a listing of game slots for a specific game location.
     *
     * @param  int  $game_location_id
     * @return \Illuminate\Http\Response
     */
    public function slotsIndex(Request $request, $game_location_id)
    {
        $this->_module = 'Game Slot';
        $this->_routePrefix = 'game.slots';
        $this->_model = new GameSlot();
        
        $this->initIndex(['game_location_id' => $game_location_id]);
        $srch_params = $request->all();
        $srch_params['game_id'] = $game_location_id;
        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;
        $this->_data['filters'] = $this->_model->getFilters('game.slots.index', $game_location_id);
        $this->_data['game_location_id'] = $game_location_id;
        
        return view('admin.game.slots.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }

    /**
     * Show the form for creating a new game slot.
     *
     * @param  int  $game_location_id
     * @return \Illuminate\Http\Response
     */
    public function slotsCreate(Request $request, $game_location_id)
    {
        $this->_module = 'Game Slot';
        $this->_routePrefix = 'game.slots';
        $this->_model = new GameSlot();
        return $this->__slotsFormUiGeneration($request, $game_location_id);
    }

    /**
     * Store a newly created game slot in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $game_location_id
     * @return \Illuminate\Http\Response
     */
    public function slotsStore(Request $request, $game_location_id)
    {
        $this->_module = 'Game Slot';
        $this->_routePrefix = 'game.slots';
        $this->_model = new GameSlot();
        return $this->__slotsFormPost($request, $game_location_id);
    }

    /**
     * Show the form for editing the specified game slot.
     *
     * @param  int  $game_location_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slotsEdit(Request $request, $game_location_id, $id)
    {
        $this->_module = 'Game Slot';
        $this->_routePrefix = 'game.slots';
        $this->_model = new GameSlot();
        return $this->__slotsFormUiGeneration($request, $game_location_id, $id);
    }

    /**
     * Update the specified game slot in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $game_location_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slotsUpdate(Request $request, $game_location_id, $id)
    {
        $this->_module = 'Game Slot';
        $this->_routePrefix = 'game.slots';
        $this->_model = new GameSlot();
        return $this->__slotsFormPost($request, $game_location_id, $id);
    }

    /**
     * Remove the specified game slot from storage.
     *
     * @param  int  $game_location_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slotsDestroy($game_location_id, $id)
    {
        $this->_model = new GameSlot();
        $response = $this->_model->remove($id);

        if($response['status'] == 200) {
            return redirect()
                ->route('game.slots.index', $game_location_id)
                ->with('success', $response['message']);
        } else {
            return redirect()
                    ->route('game.slots.index', $game_location_id)
                    ->with('error', $response['message']);
        }
    }

    /**
     * Toggle is_active status for game slot via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $game_location_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slotsToggleStatus(Request $request, $game_location_id, $id)
    {
        try {
            $model = new GameSlot();
            $data = $model->getListing(['id' => $id]);
            if (!$data) {
                return response()->json(['status' => 'error', 'message' => 'Record not found'], 404);
            }
            
            $data->is_active = $data->is_active == 1 ? 0 : 1;
            $data->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
                'is_active' => $data->is_active
            ]);
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    protected function __slotsFormUiGeneration(Request $request, $game_location_id, $id = '')
    {
        $this->initUIGeneration($id, true, ['game_location_id' => $game_location_id]);
        extract($this->_data);
        
        // Get logo file if exists
        $logoValue = null;
        if ($id && isset($data->logo) && $data->logo) {
            $logoFileModel = \App\Models\File::find($data->logo);
            if ($logoFileModel) {
                $logoValue = \App\Models\File::file($logoFileModel);
            }
        }

        $form = [
            'route'      => 'game.slots' . ($id ? '.update' : '.store'),
            'route_param' => ['game_location_id' => $game_location_id],
            'back_route' => route('game.slots.index', ['game_location_id' => $game_location_id]),
            'include_scripts' => '<script src="' . asset('assets/libs/tui-time-picker/tui-time-picker.min.js') . '"></script>',
            'fields'     => [
                'game_id'      => [
                    'type'          => 'hidden',
                    'value'         => $game_location_id,
                ],
                'name'      => [
                    'type'          => 'text',
                    'label'         => 'Name',
                    'attributes'    => [
                        'required'  => true
                    ]
                ],
                'logo'        => [
                    'type'          => 'file',
                    'label'         => 'Logo',
                    'value'         => $logoValue,
                    'attributes'    => [
                        'accept'    => 'image/*',
                    ]
                ],
                'start_time'        => [
                    'type'          => 'time',
                    'label'         => 'Start Time',
                    'attributes'    => [
                        'required'  => true
                    ]
                ],
                'end_time'        => [
                    'type'          => 'time',
                    'label'         => 'End Time',
                    'attributes'    => [
                        'required'  => true
                    ]
                ],
            ],
        ];

        return view('admin.components.admin-form', compact('data', 'id', 'form', 'breadcrumb', 'module'));
    }

    protected function __slotsFormPost(Request $request, $game_location_id, $id = '')
    {
        $validationRules = [
            'name'          => 'required|max:255',
            'start_time'    => 'required',
            'end_time'      => 'required',
        ];

        $this->validate($request, $validationRules);

        $input      = $request->all();
        $input['game_id'] = $game_location_id;
        unset($input['logo']); // Remove logo from input as it's handled separately
        $response   = $this->_model->store($input, $id, $request);
        
        if (in_array($response['status'], [200, 201])) {
            return redirect()
                ->route('game.slots.index', $game_location_id)
                ->with('success',  $response['message']);
        } else {
            return redirect()
                    ->route('game.slots.index', $game_location_id)
                    ->with('error', $response['message']);
        }
    }

    // ============ Game Modes Methods ============

    /**
     * Display a listing of game modes for a specific game slot.
     *
     * @param  int  $game_location_id
     * @param  int  $game_slot_id
     * @return \Illuminate\Http\Response
     */
    public function modesIndex(Request $request, $game_location_id, $game_slot_id)
    {
        $this->_module = 'Game Mode';
        $this->_routePrefix = 'game.modes';
        $this->_model = new GameMode();
        
        $this->initIndex(['game_location_id' => $game_location_id, 'game_slot_id' => $game_slot_id]);
        $srch_params = $request->all();
        $srch_params['slot_id'] = $game_slot_id;
        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;
        $this->_data['filters'] = $this->_model->getFilters('game.modes.index', $game_location_id, $game_slot_id);
        $this->_data['game_location_id'] = $game_location_id;
        $this->_data['game_slot_id'] = $game_slot_id;
        
        return view('admin.game.modes.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }

    /**
     * Show the form for creating a new game mode.
     *
     * @param  int  $game_location_id
     * @param  int  $game_slot_id
     * @return \Illuminate\Http\Response
     */
    public function modesCreate(Request $request, $game_location_id, $game_slot_id)
    {
        $this->_module = 'Game Mode';
        $this->_routePrefix = 'game.modes';
        $this->_model = new GameMode();
        return $this->__modesFormUiGeneration($request, $game_location_id, $game_slot_id);
    }

    /**
     * Store a newly created game mode in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $game_location_id
     * @param  int  $game_slot_id
     * @return \Illuminate\Http\Response
     */
    public function modesStore(Request $request, $game_location_id, $game_slot_id)
    {
        $this->_module = 'Game Mode';
        $this->_routePrefix = 'game.modes';
        $this->_model = new GameMode();
        return $this->__modesFormPost($request, $game_location_id, $game_slot_id);
    }

    /**
     * Show the form for editing the specified game mode.
     *
     * @param  int  $game_location_id
     * @param  int  $game_slot_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function modesEdit(Request $request, $game_location_id, $game_slot_id, $id)
    {
        $this->_module = 'Game Mode';
        $this->_routePrefix = 'game.modes';
        $this->_model = new GameMode();
        return $this->__modesFormUiGeneration($request, $game_location_id, $game_slot_id, $id);
    }

    /**
     * Update the specified game mode in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $game_location_id
     * @param  int  $game_slot_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function modesUpdate(Request $request, $game_location_id, $game_slot_id, $id)
    {
        $this->_module = 'Game Mode';
        $this->_routePrefix = 'game.modes';
        $this->_model = new GameMode();
        return $this->__modesFormPost($request, $game_location_id, $game_slot_id, $id);
    }

    /**
     * Remove the specified game mode from storage.
     *
     * @param  int  $game_location_id
     * @param  int  $game_slot_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function modesDestroy($game_location_id, $game_slot_id, $id)
    {
        $this->_model = new GameMode();
        $response = $this->_model->remove($id);

        if($response['status'] == 200) {
            return redirect()
                ->route('game.modes.index', [$game_location_id, $game_slot_id])
                ->with('success', $response['message']);
        } else {
            return redirect()
                    ->route('game.modes.index', [$game_location_id, $game_slot_id])
                    ->with('error', $response['message']);
        }
    }

    /**
     * Toggle is_active status for game mode via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $game_location_id
     * @param  int  $game_slot_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function modesToggleStatus(Request $request, $game_location_id, $game_slot_id, $id)
    {
        try {
            $model = new GameMode();
            $data = $model->getListing(['id' => $id]);
            if (!$data) {
                return response()->json(['status' => 'error', 'message' => 'Record not found'], 404);
            }
            
            $data->is_active = $data->is_active == 1 ? 0 : 1;
            $data->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully',
                'is_active' => $data->is_active
            ]);
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    protected function __modesFormUiGeneration(Request $request, $game_location_id, $game_slot_id, $id = '')
    {
        $this->initUIGeneration($id, true, ['game_location_id' => $game_location_id, 'game_slot_id' => $game_slot_id]);
        extract($this->_data);
        
        // Get logo file if exists
        $logoValue = null;
        if ($id && isset($data->logo) && $data->logo) {
            $logoFileModel = \App\Models\File::find($data->logo);
            if ($logoFileModel) {
                $logoValue = \App\Models\File::file($logoFileModel);
            }
        }

        $form = [
            'route'      => 'game.modes' . ($id ? '.update' : '.store'),
            'route_param' => ['game_location_id' => $game_location_id, 'game_slot_id' => $game_slot_id],
            'back_route' => route('game.modes.index', [$game_location_id, $game_slot_id]),
            'fields'     => [
                'slot_id'      => [
                    'type'          => 'hidden',
                    'value'         => $game_slot_id,
                ],
                'name'      => [
                    'type'          => 'text',
                    'label'         => 'Name',
                    'attributes'    => [
                        'required'  => true
                    ]
                ],
                'logo'        => [
                    'type'          => 'file',
                    'label'         => 'Logo',
                    'value'         => $logoValue,
                    'attributes'    => [
                        'accept'    => 'image/*',
                    ]
                ],
            ],
        ];

        return view('admin.components.admin-form', compact('data', 'id', 'form', 'breadcrumb', 'module'));
    }

    protected function __modesFormPost(Request $request, $game_location_id, $game_slot_id, $id = '')
    {
        $validationRules = [
            'name'          => 'required|max:255',
        ];

        $this->validate($request, $validationRules);

        $input      = $request->all();
        $input['slot_id'] = $game_slot_id;
        unset($input['logo']); // Remove logo from input as it's handled separately
        $response   = $this->_model->store($input, $id, $request);
        
        if (in_array($response['status'], [200, 201])) {
            return redirect()
                ->route('game.modes.index', [$game_location_id, $game_slot_id])
                ->with('success',  $response['message']);
        } else {
            return redirect()
                    ->route('game.modes.index', [$game_location_id, $game_slot_id])
                    ->with('error', $response['message']);
        }
    }
}
