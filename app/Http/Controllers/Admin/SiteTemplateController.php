<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;

class SiteTemplateController extends Controller {
	public function __construct($parameters = array()) {
		parent::__construct($parameters);

		$this->_module = 'Template';
		$this->_routePrefix = 'templates';
		$this->_model = new SiteTemplate();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		try {
			$this->initIndex();
			$srch_params = $request->all();
			$this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
			$this->_data['filters'] = $this->_model->getFilters();
			$this->_data['orderBy'] = $this->_model->orderBy;

			return view('admin.' . $this->_routePrefix . '.index', $this->_data)
				->with('i', ($request->input('page', 1) - 1) * $this->_offset);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
		return $this->__formUiGeneration($request);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		return $this->__formPost($request);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		try {
			$this->_data['data'] = $this->_model->getListing(['id' => $id]);
			return $this->modal();
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id) {
		return $this->__formUiGeneration($request, $id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		return $this->__formPost($request, $id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		try {
			$response = $this->_model->remove($id);

			if ($response['status'] == 200) {
				return redirect()
					->back()
					->with('success', $response['message']);
			}
			return redirect()
				->back()
				->with('error', $response['message']);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	/**
	 * ui parameters for form add and edit
	 *
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	protected function __formUiGeneration(Request $request, $id = '') {
		$response = $this->initUIGeneration($id, true, ['template_type' => $request->get('template_type')]);
		if ($response) {
			return $response;
		}

		extract($this->_data);
		$labelWidth = 'col-lg-2 col-md-2 col-sm-4 col-xs-12';
		$fieldWidth = 'col-lg-6 col-md-6 col-sm-6 col-xs-12';
		$status = \App\Helpers\Helper::makeSimpleArray($this->_model->statuses, 'id,name');
		$pdfPaperSizes = \App\Helpers\Helper::makeSimpleArray($this->_model->pdfPaperSizes, 'id,name');
		$pdfPaperLayouts = \App\Helpers\Helper::makeSimpleArray($this->_model->pdfPaperLayouts, 'id,name');
		$templateTypes = \App\Helpers\Helper::makeSimpleArray($this->_model->templateTypes, 'id,name');
		$form = [
			'route' => $this->_routePrefix . ($id ? '.update' : '.store'),
			'back_route' => route($this->_routePrefix . '.index', ['template_type' => $request->get('template_type')]),
			'include_scripts' => '<script src="' . asset('admin-form-plugins/form-controls.js') . '"></script>',
			'fields' => [
				'template_type' => [
					'type' => 'radio',
					'label' => 'Template Type',
					'options' => $templateTypes,
					'value' => $data->template_type ? $data->template_type : ($request->get('template_type') ?? 1),
					'label_width' => $labelWidth,
					'field_width' => $fieldWidth,
					'attributes' => [
						'width' => 'col-lg-3 col-md-3 col-sm-12 col-xs-12',
					],
				],
				'template_name' => [
					'type' => !$id ? 'text' : 'label',
					'label' => 'Template Type',
					'help' => 'Maximum 50 characters',
					'attributes' => ['required' => true],
					'value' => $data->template_name,
					'label_width' => $labelWidth,
					'field_width' => $fieldWidth,
				],
				'subject' => [
					'type' => 'text',
					'label' => 'Email Subject',
					'help' => 'Maximum 255 characters',
					'attributes' => ['required' => true],
					'label_width' => $labelWidth,
					'field_width' => $fieldWidth,
				],
				'template_content' => [
					'type' => 'editor',
					'label' => 'Email Body',
					'label_width' => $labelWidth,
					'field_width' => 'col-lg-10 col-md-10 col-sm-8 col-xs-12',
				],
				'template_content_sms' => [
					'type' => 'textarea',
					'label' => 'Message Body',
					'label_width' => $labelWidth,
					'field_width' => $fieldWidth,
					'value' => $data->subject,
				],
				'pdf_paper_size' => [
					'type' => 'radio',
					'label' => 'Paper Size',
					'options' => $pdfPaperSizes,
					'value' => isset($data->pdf_paper_size) ? $data->pdf_paper_size : 4,
					'label_width' => $labelWidth,
					'field_width' => $fieldWidth,
					'attributes' => [
						'width' => 'col-lg-1 col-md-1 col-sm-12 col-xs-12',
					],
				],
				'pdf_paper_layout' => [
					'type' => 'radio',
					'label' => 'Paper Layout',
					'options' => $pdfPaperLayouts,
					'value' => isset($data->pdf_paper_layout) ? $data->pdf_paper_layout : 0,
					'label_width' => $labelWidth,
					'field_width' => $fieldWidth,
					'attributes' => [
						'width' => 'col-lg-3 col-md-3 col-sm-12 col-xs-12',
					],
				],
				'status' => [
					'type' => 'radio',
					'label' => 'Status',
					'options' => $status,
					'value' => isset($data->status) ? $data->status : 1,
					'label_width' => $labelWidth,
					'field_width' => $fieldWidth,
					'attributes' => [
						'width' => 'col-lg-3 col-md-3 col-sm-12 col-xs-12',
					],
				],
			],
		];

		if ($id) {
			unset($form['fields']['template_type']['help']);
		}

		return view('admin.components.admin-form', compact('data', 'id', 'form', 'breadcrumb', 'module'));
	}

	/**
	 * Form post action
	 *
	 * @param  Request $request [description]
	 * @param  string  $id      [description]
	 * @return [type]           [description]
	 */
	protected function __formPost(Request $request, $id = '') {
		$input = $request->all();
		$validationRules = [
			'subject' => 'required|max:255',
			'footer_note' => 'nullable|max:255',
			'template_content' => 'required',
		];

		if ($input['template_type'] == '2') {
			$validationRules['subject'] = 'nullable';
			$validationRules['template_content'] = 'nullable';
			$validationRules['template_content_sms'] = 'required';
		} elseif ($input['template_type'] == '3') {
			$validationRules['subject'] = 'nullable|max:255';
		} elseif ($input['template_type'] == '4') {
			$validationRules['subject'] = 'nullable|max:255';
			$validationRules['template_content'] = 'nullable';
			$validationRules['template_content_whatsapp'] = 'required';
		}

		$this->validate($request, $validationRules);

		$input = $request->all();
		$response = $this->_model->store($input, $id, $request);

		if (in_array($response['status'], [200, 201])) {
			return redirect()
				->back()
				->with('success', $response['message']);
		} else {
			return redirect()
				->back()
				->with('error', $response['message']);
		}
	}
}
