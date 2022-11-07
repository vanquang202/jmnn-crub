<?php

namespace Jmnn\Crub\Crub;

use App\Http\Controllers\Controller;
use Arr;
use Jmnn\Crub\Http\Requests\CrubRequest;

abstract class JmnnCrub  extends Controller
{

    public $model;
    public $views = [
        'list' => 'crub::index',
        'create' => 'crub::create',
        'edit' => 'crub::create',
        'router-list' => '/'
    ];

    public $data = [];
    public $except = ['_token'];

    public function __construct()
    {
        $class =  explode('\\',get_called_class());
        $this->model = '\\App\\Models\\' . str_replace('Controller','',$class[count($class)-1]);
        $this->data['header'] = 'header';
        $this->data['table'] = $this->model;
        $this->data['main'] = 'main';
        $this->data['layout'] = 'crub::default';
        $this->data['footer'] = 'footer';
        $this->data['config'] = [
            'create' => $this->route['create'] ?? 'test.create',
            'delete' => $this->route['delete'] ??  'test.delete',
            'edit' => $this->route['edit'] ??  'test.edit',
        ];
        $this->data['rolColume'] = [];
        $this->data['medias'] = [];
        $this->data['dataMuntipleStatus'] = [];

        if($this->__methodExists('beforeMount')) $this->beforeMount();
    }



    public function upLoadImage($image,$dataImageModleExistByUpdate = null)
    {
        try {
            if($dataImageModleExistByUpdate) $this->__checkImageExist($dataImageModleExistByUpdate);
            $nameImage = uniqid() . '.' . $image->getClientOriginalExtension();

            if(method_exists($this,'createUploadImage'))
            {
                $this->createUploadImage($nameImage);
            }else{
                $image->move(public_path($this->imagePath ?? 'images'), $nameImage);
            }

            return $nameImage;
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function __checkImageExist($dataImageModleExistByUpdate,  $dataImageHasMultiple = [])
    {
        if(count($dataImageHasMultiple) == 0):
            $this->__unLinkImage($dataImageModleExistByUpdate);
            return ;
        else:
            foreach($dataImageHasMultiple as $image)
            {
                $this->unLinkImage($image);
            }
        endif;
    }

    private function __unLinkImage($image)
    {
        if(method_exists($this,'createUnLinkImage'))
        {
            $this->createUnLinkImage($image);

        }else{
            if (file_exists(public_path($this->imagePath ?? 'images') . '/' . $image)) {
                unlink(public_path($this->imagePath ?? 'images') . '/' . $image);
            };
        }
    }

    public function index()
    {
        if(!$this->data['data'] = $this->__methodExists('getDataIndex'))
            $this->data['data'] =$this->model::all()->toArray();

        return view($this->views['list'], $this->data);
    }

    public function create()
    {
        if(!$this->data['data'] = $this->__methodExists('getDataCreate'))
            $this->data['data'] = [];
        return view($this->views['create'], $this->data);
    }

    private function __getDataRequest($data, $dataModelExitsByUpdate = null)
    {
        if (isset($data['image']) && isset($data['images'])) return $data = $this->getDataHasAllImage($data, $dataModelExitsByUpdate);
        if (isset($data['images'])) return $data = $this->getDataHasImage($data, $dataModelExitsByUpdate);
        if (isset($data['image'])) return $data = $this->getDataHasImages($data, $dataModelExitsByUpdate);
        return $data;
    }

    private function __getDataHasImage($data, $dataModelExitsByUpdate = null)
    {
        $dataImageModleExistByUpdate = null;
        if ($dataModelExitsByUpdate) $dataImageModleExistByUpdate = $dataModelExitsByUpdate->image;
        $nameImage = $this->upLoadImage($data['image'], $dataImageModleExistByUpdate);
        $dataResult = Arr::except($data, ['image']);
        $dataResult['image']  = $nameImage;
        return $dataResult;
    }

    private function __getDataHasImages($data, $dataModelExitsByUpdate = null)
    {
        $arrayImages = [];
        foreach ($data['images'] as $image) {
            $nameImage = $this->upLoadImage($image);
            if ($nameImage) array_push($arrayImages, $nameImage);
        }
        $dataResult = Arr::except($data, ['images']);
        $dataResult['images']  = json_encode($arrayImages);
        return $dataResult;
    }

    private function __getImageJsonDeCodeModelExitst($dataModelExitsByUpdate)
    {
        $images = json_decode($dataModelExitsByUpdate->images);
        return $images;
    }

    private function __getDataHasAllImage($data, $dataModelExitsByUpdate = null)
    {
        $data = $this->getDataHasImages($this->getDataHasImage($data, $dataModelExitsByUpdate), $dataModelExitsByUpdate);
        return $data;
    }

    private function __redirectErrorNullModel($data)
    {
        return redirect()->back()->withInput()->with('error', $data['message']);
    }

    private function __redirectSuccessModel($data)
    {
        return redirect($this->views[$data['route']])->with('success', $data['message']);
    }

    public function store(CrubRequest $request)
    {
        if(!$data = $this->__methodExists('createStore'))
            $data = $this->model::create($this->__getDataRequest($request->except($this->except)));

        if (!$data) return $this->redirectErrorNullModel([
            'message' => 'Thêm mới thất bại !'
        ]);

        return $this->__redirectSuccessModel([
            'route' => 'router-list',
            'message' => 'Thêm mới thành công',
        ]);
    }

    public function edit($id)
    {
        if(!$this->data['data'] = $this->__methodExists('getDataEdit',$id))
            $this->data['data'] = [];
        return view($this->views['edit'],$this->data);
    }

    public function update(CrubRequest $request, $id)
    {
        $model = $this->model::find((int)$id);
        if(!$this->__methodExists('createUpdate',$id))
            $this->model::find($id)->update($this->__getDataRequest($request->except($this->except), $model));

        return $this->__redirectSuccessModel([
            'route' => 'router-list',
            'message' => 'Cập nhật thành công',
        ]);
    }

    public function destroy($id)
    {
        if(!$this->__methodExists('createDestroy',$id))
            $this->model::destroy($id);$this->model::destroy($id);

        return $this->__redirectSuccessModel([
            'route' => 'router-list',
            'message' => 'Xóa bản ghi thành công',
        ]);
    }

    private function __methodExists($method,...$params)
    {
        if(method_exists($this,$method))
        {
            return   $this->$method(...$params);;
        }
        return false;
    }

    abstract public function getRules(): array  ;
}
