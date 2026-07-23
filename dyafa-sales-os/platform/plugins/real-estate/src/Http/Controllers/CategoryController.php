<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Requests\UpdateTreeCategoryRequest;
use Botble\RealEstate\Forms\CategoryForm;
use Botble\RealEstate\Http\Requests\CategoryRequest;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends BaseController
{
    public function __construct(protected CategoryInterface $categoryRepository)
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::category.name'), route('property_category.index'));
    }

    public function index(Request $request)
    {
        $this->pageTitle(trans('plugins/real-estate::category.name'));

        $categories = $this->categoryRepository->getCategories(['*'], [
            'created_at' => 'DESC',
            'is_default' => 'DESC',
            'order' => 'ASC',
        ]);

        $categories->loadMissing('slugable')->loadCount(['properties', 'projects']);

        if ($request->ajax()) {
            $data = view('core/base::forms.partials.tree-categories', $this->getOptions(compact('categories')))->render();

            return $this
                ->httpResponse()
                ->setData($data);
        }

        Assets::addStylesDirectly(['vendor/core/core/base/css/tree-category.css'])
            ->addScriptsDirectly(['vendor/core/core/base/js/tree-category.js']);

        $form = CategoryForm::create(['template' => 'core/base::forms.form-tree-category']);
        $form = $this->setFormOptions($form, null, compact('categories'));

        return $form->renderForm();
    }

    public function create(Request $request)
    {
        $this->pageTitle(trans('plugins/real-estate::category.create'));

        if ($request->ajax()) {
            return $this
                ->httpResponse()
                ->setData($this->getForm());
        }

        return CategoryForm::create()->renderForm();
    }

    public function store(CategoryRequest $request)
    {
        if ($request->input('is_default')) {
            Category::query()->where('id', '>', 0)->update(['is_default' => 0]);
        }

        $category = Category::query()->create($request->input());

        event(new CreatedContentEvent(PROPERTY_CATEGORY_MODULE_SCREEN_NAME, $request, $category));

        if ($request->ajax()) {
            $category = Category::query()->findOrFail($category->getKey());

            if ($request->input('submit') == 'save') {
                $form = $this->getForm();
            } else {
                $form = $this->getForm($category);
            }

            $this
                ->httpResponse()
                ->setData([
                'model' => $category,
                'form' => $form,
            ]);
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property_category.index'))
            ->setNextUrl(route('property_category.edit', $category->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Category $category, Request $request)
    {
        event(new BeforeEditContentEvent($request, $category));

        if ($request->ajax()) {
            return $this
                ->httpResponse()
                ->setData($this->getForm($category));
        }

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $category->name]));

        return CategoryForm::createFromModel($category)->renderForm();
    }

    public function update(Category $category, CategoryRequest $request)
    {
        if ($request->input('is_default')) {
            Category::query()->where('id', '!=', $category->getKey())->update(['is_default' => 0]);
        }

        $category->fill($request->input());

        $category->save();

        event(new UpdatedContentEvent(PROPERTY_CATEGORY_MODULE_SCREEN_NAME, $request, $category));

        if ($request->ajax()) {
            if ($request->input('submit') == 'save') {
                $form = $this->getForm();
            } else {
                $form = $this->getForm($category);
            }

            $this
                ->httpResponse()
                ->setData([
                'model' => $category,
                'form' => $form,
            ]);
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property_category.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Category $category, Request $request)
    {
        try {
            $category->delete();

            event(new DeletedContentEvent(PROPERTY_CATEGORY_MODULE_SCREEN_NAME, $request, $category));

            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function updateTree(UpdateTreeCategoryRequest $request)
    {
        Category::updateTree($request->validated('data'));

        return $this
            ->httpResponse()
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    protected function getForm(?Category $model = null)
    {
        $options = ['template' => 'core/base::forms.form-no-wrap'];

        if ($model) {
            $options['model'] = $model;
        }

        $form = app(FormBuilder::class)->create(CategoryForm::class, $options);

        $form = $this->setFormOptions($form, $model);

        return $form->renderForm();
    }

    protected function setFormOptions(FormAbstract $form, ?Category $model = null, array $options = [])
    {
        if (! $model) {
            $form->setUrl(route('property_category.create'));
        }

        if (! Auth::user()->hasPermission('property_category.create') && ! $model) {
            $class = $form->getFormOption('class');
            $form->setFormOption('class', $class . ' d-none');
        }

        $form->setFormOptions($this->getOptions($options));

        return $form;
    }

    protected function getOptions(array $options = [])
    {
        return array_merge([
            'canCreate' => Auth::user()->hasPermission('property_category.create'),
            'canEdit' => Auth::user()->hasPermission('property_category.edit'),
            'canDelete' => Auth::user()->hasPermission('property_category.destroy'),
            'createRoute' => 'property_category.create',
            'editRoute' => 'property_category.edit',
            'deleteRoute' => 'property_category.destroy',
            'updateTreeRoute' => 'property_category.update-tree',
        ], $options);
    }
}
