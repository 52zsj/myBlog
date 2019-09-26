<?php


namespace app\admin\controller;

use app\admin\logic\Auth;
use app\common\exception\Failure;
use app\common\exception\Success;
use app\common\library\Random;
use app\common\model\Admin as AdminModel;
use app\common\model\Dictionary as DictionaryModel;

use think\App;

class Dictionary extends Base
{
    public function __construct(App $app = null) {
        parent::__construct($app);
    }

    public function index() {
        $row = DictionaryModel::paginate();
        $pages = $row->render();
        $this->assign('page', $pages);
        $this->assign('row', $row);
        return $this->fetch();
    }

    public function add() {
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            $result = AdminModel::create($params);
            if ($result) {
                throw new Success('添加成功');
            } else {
                throw new Failure('添加失败');
            }
        }
        return $this->fetch();
    }
}