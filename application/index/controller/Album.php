<?php


namespace app\index\controller;


use app\common\exception\Failure;
use app\common\exception\Success;
use app\index\logic\Widget as WidgetLogic;
use think\App;
use app\common\model\Album as AlbumModel;

class Album extends Base
{
    protected $pageSize = 8;

    public function initialize() {
        parent::initialize();
        $this->model = new AlbumModel();


    }

    public function index() {
        //首页吧
        $where['status'] = 1;
        $this->assign('menu_id',3);
        $count = $this->model->where($where)->count();
        $this->assignConfig('request_url', url('index/album/index'));
        if ($this->request->isAjax()) {
            $offset = $this->request->param('offset', '0');
            $limit = $this->pageSize;
            $albumTotal = $this->model->where($where)->with('item')->count();
            $row = $this->model->where($where)->with(['item'])->field('id,title,cover,description,music')->page($offset, $limit)->select();
            throw new Success('加载成功', ['row' => $row, 'total' => $albumTotal, 'limit' => $limit]);
        }
        WidgetLogic::inspirational();
        WidgetLogic::hotArticle();
        WidgetLogic::tagCloud();
        $this->assignConfig('request_url', url('index/album/index'));
        // 模板变量赋值
        return $this->fetch();
    }

    public function index_bak() {
        //首页吧
        $where['status'] = 1;
        $count = $this->model->where($where)->count();
        $this->assignConfig('request_url', url('index/album/index'));
        if ($this->request->isAjax()) {
            $offset = $this->request->param('offset', '0');
            $limit = $this->pageSize;
            $albumTotal = $this->model->where($where)->count();
            $row = $this->model->where($where)->field('id,title,cover,description')->page($offset, $limit)->select()->toArray();
            foreach ($row as $k => &$v) {
                $v['detail_url'] = url('index/album/detail', ['album_id' => $v['id']]);
            }
            unset($v);
            throw new Success('加载成功', ['row' => $row, 'total' => $albumTotal, 'limit' => $limit]);
        }
        WidgetLogic::inspirational();
        WidgetLogic::hotArticle();
        WidgetLogic::tagCloud();
        $this->assignConfig('request_url', url('index/album/index'));
        $this->assignConfig(['page_data' => ['total' => $count, 'limit' => $this->pageSize]]);
        // 模板变量赋值
        return $this->fetch();
    }

    public function detail() {
        $id = $this->request->param('album_id');
        if (!$id) {
            $this->error('数据异常');
        }
        $where = ['id' => $id, 'status' => 1];
        $row = $this->model->where($where)->with('item')->find();
        $count = count($row['item']);
        $this->assign('row', $row);
        $this->assign('count', $count);
        return $this->fetch('detail');
    }
}